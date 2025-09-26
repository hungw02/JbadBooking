<?php

namespace App\Http\Controllers;

use App\Models\SingleBooking;
use App\Models\SubscriptionBooking;
use App\Models\Court;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingUpdateController extends BaseBookingController
{

    // Hiển thị form cập nhật đơn đặt sân đơn lẻ
    public function showSingleBookingUpdateForm($id)
    {
        $user = Auth::user();
        $isOwner = $user->role === 'owner';

        // Lấy đơn đặt
        if ($isOwner) {
            $booking = SingleBooking::with('court')->findOrFail($id);
            $courts = Court::where('status', 'available')->get();

            return view('owner.booking.single-detail', compact('booking', 'courts'));
        } else {
            $booking = SingleBooking::where('id', $id)
                ->where('customer_id', $user->id)
                ->where('status', 'confirmed')
                ->with('court')
                ->firstOrFail();

            if (Carbon::parse($booking->start_time) <= now()) {
                return redirect()->route('booking.list')
                    ->with('error', 'Không thể thay đổi sân cho lịch đặt này.');
            }

            $startDateTime = Carbon::parse($booking->start_time);
            $endDateTime = Carbon::parse($booking->end_time);
            $date = $startDateTime->format('Y-m-d');

            // Get all available courts
            $allCourts = Court::where('status', 'available')
                ->where('id', '!=', $booking->court_id)
                ->get();

            // Check each court for availability
            $availableCourts = [];
            foreach ($allCourts as $court) {
                $conflicts = $this->checkConflicts(
                    $court->id,
                    $startDateTime,
                    $endDateTime
                );

                if (!$conflicts) {
                    $availableCourts[] = $court;
                }
            }

            return view('customer.booking.booking-single-change', [
                'booking' => $booking,
                'availableCourts' => $availableCourts
            ]);
        }
    }

    //Hiển thị form cập nhật đơn đặt sân định kỳ

    public function showSubscriptionBookingUpdateForm($id)
    {
        $user = Auth::user();
        $isOwner = $user->role === 'owner';

        // Get booking
        if ($isOwner) {
            $booking = SubscriptionBooking::with('court')->findOrFail($id);
            $courts = Court::where('status', 'available')->get();

            return view('owner.booking.subscription-detail', compact('booking', 'courts'));
        } else {
            $booking = SubscriptionBooking::where('id', $id)
                ->where('customer_id', $user->id)
                ->where('status', 'confirmed')
                ->with('court')
                ->firstOrFail();

            if (Carbon::parse($booking->end_date) <= now()) {
                return redirect()->route('booking.list')
                    ->with('error', 'Không thể thay đổi sân cho lịch đặt định kỳ này.');
            }

            // Get available courts
            $dayOfWeek = $booking->day_of_week;
            $startTime = $booking->start_time;
            $endTime = $booking->end_time;
            $startDate = Carbon::parse($booking->start_date);
            $endDate = Carbon::parse($booking->end_date);

            // Tìm các sân khả dụng
            $availableCourts = $this->findAvailableCourts(
                $dayOfWeek,
                $startTime,
                $endTime,
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d'),
                [$booking->court_id]
            );

            return view('customer.booking.booking-subscription-change', [
                'booking' => $booking,
                'availableCourts' => $availableCourts
            ]);
        }
    }

    //Cập nhật đơn đặt sân đơn lẻ
    public function updateSingleBooking(Request $request, $id)
    {
        $user = Auth::user();
        $isOwner = $user->role === 'owner';

        // Get booking
        if ($isOwner) {
            $booking = SingleBooking::findOrFail($id);
        } else {
            $booking = SingleBooking::where('id', $id)
                ->where('customer_id', $user->id)
                ->firstOrFail();
        }

        // Check booking status
        if ($booking->status == 'cancelled' || $booking->status == 'completed') {
            $message = 'Không thể cập nhật đơn đặt sân đã hủy hoặc đã hoàn thành!';

            $redirectRoute = $isOwner
                ? route('owner.bookings.single', $booking->id)
                : route('booking.detail', $booking->id);

            return redirect($redirectRoute)->with('error', $message);
        }

        // Check if booking time is in the past
        if (!$isOwner && Carbon::parse($booking->start_time) <= now()) {
            return redirect()->route('booking.detail', $booking->id)
                ->with('error', 'Không thể thay đổi đơn đặt sân đã diễn ra!');
        }

        // Validate request
        $request->validate([
            'court_id' => 'required|exists:courts,id',
        ]);

        // For owner: additionally validate time fields if provided
        if ($isOwner && $request->has('start_time') && $request->has('end_time')) {
            $startTime = Carbon::parse($request->input('start_time'));
            $endTime = Carbon::parse($request->input('end_time'));

            // Check if end time is after start time
            if ($endTime->lessThanOrEqualTo($startTime)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Thời gian kết thúc phải sau thời gian bắt đầu!');
            }

            // Check minimum duration (1 hour)
            $diffInMinutes = $startTime->diffInMinutes($endTime);
            if ($diffInMinutes < 60) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Thời gian chơi phải ít nhất 1 tiếng!');
            }
        } else {
            // For customers or when times are not provided, use existing times
            $startTime = Carbon::parse($booking->start_time);
            $endTime = Carbon::parse($booking->end_time);
        }

        // Check for conflicts
        $conflicts = $this->checkConflicts($request->court_id, $startTime, $endTime, $booking->id);

        if ($conflicts) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Sân đã được đặt trong khoảng thời gian này!');
        }

        try {
            DB::beginTransaction();

            // Create update data
            $updateData = [
                'court_id' => $request->court_id
            ];

            // For owners, also update time if provided
            if ($isOwner && $request->has('start_time') && $request->has('end_time')) {
                $updateData['start_time'] = $startTime->format('Y-m-d H:i:s');
                $updateData['end_time'] = $endTime->format('Y-m-d H:i:s');
                
                // Recalculate price if time has changed
                if ($booking->start_time != $startTime->format('Y-m-d H:i:s') || 
                    $booking->end_time != $endTime->format('Y-m-d H:i:s')) {
                    
                    // Calculate new price
                    $newPrice = $this->calculatePriceInternal($startTime, $endTime);
                    
                    // Apply discount if exists
                    if ($booking->discount_percent > 0) {
                        $discount = $newPrice * ($booking->discount_percent / 100);
                        $newPrice = $newPrice - $discount;
                    }
                    
                    $updateData['total_price'] = $newPrice;
                }
            }

            // Update booking
            $booking->update($updateData);

            DB::commit();

            $successMessage = 'Cập nhật đơn đặt sân thành công!';

            if ($isOwner) {
                return redirect()->route('owner.bookings.single', $booking->id)
                    ->with('success', $successMessage);
            } else {
                return redirect()->route('booking.detail', $booking->id)
                    ->with('success', $successMessage);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }


    //Cập nhật đơn đặt sân định kỳ

    public function updateSubscriptionBooking(Request $request, $id)
    {
        $user = Auth::user();
        $isOwner = $user->role === 'owner';

        // Get booking
        if ($isOwner) {
            $booking = SubscriptionBooking::findOrFail($id);
        } else {
            $booking = SubscriptionBooking::where('id', $id)
                ->where('customer_id', $user->id)
                ->firstOrFail();
        }

        // Check booking status
        if ($booking->status == 'cancelled' || $booking->status == 'completed') {
            $message = 'Không thể cập nhật đơn đặt sân định kỳ đã hủy hoặc đã hoàn thành!';

            $redirectRoute = $isOwner
                ? route('owner.bookings.subscription', $booking->id)
                : route('booking.subscription.detail', $booking->id);

            return redirect($redirectRoute)->with('error', $message);
        }

        // Check if booking period has ended
        if (!$isOwner && Carbon::parse($booking->end_date) <= now()) {
            return redirect()->route('booking.subscription.detail', $booking->id)
                ->with('error', 'Không thể thay đổi đơn đặt sân đã kết thúc!');
        }

        // Validate court ID
        $request->validate([
            'court_id' => 'required|exists:courts,id',
        ]);

        // Additional validation for owner
        if ($isOwner) {
            $request->validate([
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
            ]);
        }

        // Prepare update data
        $updateData = [
            'court_id' => $request->court_id
        ];

        // For owners, update additional fields if provided
        if ($isOwner) {
            if ($request->has('start_time') && $request->has('end_time')) {
                $startTime = Carbon::parse($request->start_time)->format('H:i:s');
                $endTime = Carbon::parse($request->end_time)->format('H:i:s');
                $updateData['start_time'] = $startTime;
                $updateData['end_time'] = $endTime;
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $updateData['start_date'] = Carbon::parse($request->start_date)->format('Y-m-d');
                $updateData['end_date'] = Carbon::parse($request->end_date)->format('Y-m-d');
            }
        }

        // Check for conflicts
        $dayOfWeek = isset($updateData['day_of_week']) ? $updateData['day_of_week'] : $booking->day_of_week;
        $startTime = isset($updateData['start_time']) ? $updateData['start_time'] : $booking->start_time;
        $endTime = isset($updateData['end_time']) ? $updateData['end_time'] : $booking->end_time;
        $startDate = isset($updateData['start_date']) ? Carbon::parse($updateData['start_date']) : Carbon::parse($booking->start_date);
        $endDate = isset($updateData['end_date']) ? Carbon::parse($updateData['end_date']) : Carbon::parse($booking->end_date);

        // Format dates for comparison
        $startDateFormatted = $startDate->format('Y-m-d');
        $endDateFormatted = $endDate->format('Y-m-d');

        // Sử dụng BookingConflictService để kiểm tra xung đột
        $conflictService = app(\App\Services\BookingConflictService::class);
        $conflicts = $conflictService->checkSubscriptionBookingConflicts(
            $request->court_id,
            $dayOfWeek,
            $startTime,
            $endTime,
            $startDateFormatted,
            $endDateFormatted,
            $booking->id
        );

        if (!empty($conflicts)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Sân đã được đặt trong khoảng thời gian này!');
        }

        try {
            DB::beginTransaction();

            // Luôn tính toán lại giá khi cập nhật đơn hàng (bất kể có thay đổi thời gian hay không)
            if ($isOwner) {
                // Tính số buổi
                $startDateValue = isset($updateData['start_date']) ? $updateData['start_date'] : $booking->start_date;
                $endDateValue = isset($updateData['end_date']) ? $updateData['end_date'] : $booking->end_date;
                
                $sessionCount = $this->countSessions(
                    $startDateValue,
                    $endDateValue,
                    $dayOfWeek
                );
                
                // Đảm bảo định dạng thời gian đúng cho việc tính toán giá
                // Sử dụng ngày cụ thể đúng với ngày trong tuần để tính giá đúng
                $baseDate = Carbon::parse('2000-01-01');
                while ($baseDate->format('N') + 1 != $dayOfWeek) {
                    $baseDate->addDay();
                }
                
                $startTimeFormatted = $baseDate->format('Y-m-d') . ' ' . $startTime;
                $endTimeFormatted = $baseDate->format('Y-m-d') . ' ' . $endTime;
                
                $startTimeObj = Carbon::parse($startTimeFormatted);
                $endTimeObj = Carbon::parse($endTimeFormatted);
                
                // Xử lý trường hợp kết thúc là 00:00
                if ($endTimeObj->format('H:i') === '00:00') {
                    $endTimeObj->addDay();
                }
                
                // Tính giá một buổi
                $priceService = app(\App\Services\BookingPriceService::class);
                $singleSessionPrice = $priceService->calculatePrice($startTimeObj, $endTimeObj);
                
                // Tính tổng giá gốc
                $originalTotalPrice = $singleSessionPrice * $sessionCount;
                
                // Giữ nguyên discount_percent từ đơn hàng ban đầu
                $discountPercent = $booking->discount_percent;
                
                // Áp dụng khuyến mãi nếu có
                $finalTotalPrice = $originalTotalPrice;
                if ($discountPercent > 0) {
                    $discount = $originalTotalPrice * ($discountPercent / 100);
                    $finalTotalPrice = $originalTotalPrice - $discount;
                }
                
                // Lưu giá mới
                $updateData['total_price'] = $finalTotalPrice;
            }

            // Update booking
            $booking->update($updateData);

            DB::commit();

            $successMessage = 'Cập nhật đơn đặt sân định kỳ thành công!';

            if ($isOwner) {
                return redirect()->route('owner.bookings.subscription', $booking->id)
                    ->with('success', $successMessage);
            } else {
                return redirect()->route('booking.subscription.detail', $booking->id)
                    ->with('success', $successMessage);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
