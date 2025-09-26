<?php

namespace App\Http\Controllers;

use App\Models\SingleBooking;
use App\Models\SubscriptionBooking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CustomerBookingManagerController extends BaseBookingController
{
    // Hiển thị trang quản lý lịch đặt
    public function index()
    {
        $user = Auth::user();

        // Lấy danh sách lịch đặt theo buổi
        $singleBookings = SingleBooking::where('customer_id', $user->id)
            ->with('court')
            ->orderBy('start_time', 'desc')
            ->paginate(6);

        // Lấy danh sách lịch đặt định kỳ
        $subscriptionBookings = SubscriptionBooking::where('customer_id', $user->id)
            ->with('court')
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        return view('customer.booking.booking-list', [
            'singleBookings' => $singleBookings,
            'subscriptionBookings' => $subscriptionBookings
        ]);
    }

    //Hiển thị chi tiết lịch đặt theo buổi
    public function showDetail($id)
    {
        $user = Auth::user();
        $booking = SingleBooking::where('id', $id)
            ->where('customer_id', $user->id)
            ->with(['court', 'refunds'])
            ->first();

        if (!$booking) {
            return redirect()->route('booking.list')
                ->with('error', 'Không tìm thấy thông tin lịch đặt hoặc bạn không có quyền xem thông tin này.');
        }

        // Get the first refund record if exists
        if ($booking->status === 'cancelled') {
            $refunds = $booking->refunds;
            if ($refunds && $refunds->count() > 0) {
                $booking->refund = $refunds->first();
            }
        }

        return view('customer.booking.booking-single-detail', [
            'booking' => $booking
        ]);
    }

    //Hiển thị chi tiết lịch đặt định kỳ
    public function showSubscriptionDetail($id)
    {
        $user = Auth::user();
        $booking = SubscriptionBooking::where('id', $id)
            ->where('customer_id', $user->id)
            ->with(['court', 'refunds'])
            ->first();

        if (!$booking) {
            return redirect()->route('booking.list')
                ->with('error', 'Không tìm thấy thông tin lịch đặt định kỳ hoặc bạn không có quyền xem thông tin này.');
        }

        // Get the first refund record if exists
        if ($booking->status === 'cancelled') {
            $refunds = $booking->refunds;
            if ($refunds && $refunds->count() > 0) {
                $booking->refund = $refunds->first();
            }
        }

        // Đếm số buổi còn lại
        $totalSessions = 0;
        $remainingSessions = 0;

        $startDate = Carbon::parse($booking->start_date);
        $endDate = Carbon::parse($booking->end_date);
        $dayOfWeek = $booking->day_of_week;
        $now = Carbon::now();

        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $carbonDayOfWeek = $currentDate->dayOfWeek;
            $ourDayOfWeek = $carbonDayOfWeek === 0 ? 8 : $carbonDayOfWeek + 1;

            if ($ourDayOfWeek == $dayOfWeek) {
                $totalSessions++;

                // Chỉ tính buổi còn trong tương lai
                $sessionStartTime = Carbon::parse($currentDate->format('Y-m-d') . ' ' . $booking->start_time);
                if ($sessionStartTime > $now) {
                    $remainingSessions++;
                }
            }
            $currentDate->addDay();
        }

        return view('customer.booking.booking-subscription-detail', [
            'booking' => $booking,
            'totalSessions' => $totalSessions,
            'remainingSessions' => $remainingSessions
        ]);
    }
}
