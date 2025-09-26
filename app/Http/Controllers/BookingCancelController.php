<?php

namespace App\Http\Controllers;

use App\Models\SingleBooking;
use App\Models\SubscriptionBooking;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BookingCancelController extends BaseBookingController
{
    //Hủy đơn đặt sân đơn lẻ
    public function cancelSingleBooking(Request $request, $id)
    {
        $booking = SingleBooking::findOrFail($id);
        $user = Auth::user();
        $isOwner = $user->role === 'owner';

        // Kiểm tra quyền hủy đơn
        if (!$isOwner && $booking->customer_id !== $user->id) {
            return back()->with('error', 'Bạn không có quyền hủy đơn đặt sân này!');
        }

        // Kiểm tra trạng thái đơn
        if ($booking->status == 'cancelled' || $booking->status == 'completed') {
            $message = $isOwner
                ? 'Không thể hủy đơn đặt sân đã hủy hoặc đã hoàn thành!'
                : 'Đơn đặt sân đã hủy hoặc đã hoàn thành, không thể hủy lại!';

            $redirectRoute = $isOwner
                ? route('owner.bookings.single', $booking->id)
                : route('booking.detail', $booking->id);

            return redirect($redirectRoute)->with('error', $message);
        }

        // Tính số tiền thực tế đã thanh toán (đặt cọc hoặc thanh toán đầy đủ)
        $actualPaidAmount = $booking->payment_type === 'deposit' 
            ? $booking->total_price * 0.5 
            : $booking->total_price;

        // Xử lý dữ liệu refund
        if ($isOwner) {
            // Chủ sân tự nhập lý do và số tiền hoàn trả
            $data = $request->validate([
                'refund_amount' => 'required|numeric|min:0|max:' . $actualPaidAmount,
                'refund_reason' => 'required|string|max:255',
            ]);

            $refundAmount = $data['refund_amount'];
            $refundReason = $data['refund_reason'];
        } else {
            // Người dùng: lý do và số tiền tự động theo trường hợp
            $now = now();
            $startTime = \Carbon\Carbon::parse($booking->start_time);
            $bookingCreatedTime = \Carbon\Carbon::parse($booking->created_at);
            $minutesSinceBooking = $now->diffInMinutes($bookingCreatedTime);
            $hoursUntilStart = $now->diffInHours($startTime, false);

            if ($minutesSinceBooking <= 5) {
                // Hủy đơn trong vòng 5 phút sau khi đặt: hoàn 100% số tiền đã thanh toán
                $refundAmount = $actualPaidAmount;
                $refundReason = 'Hủy trong vòng 5 phút sau khi đặt: hoàn 100%.';
            } else if ($hoursUntilStart > 24) {
                // Hủy đơn trước giờ chơi trên 24 tiếng: hoàn 50% số tiền đã thanh toán
                $refundAmount = $actualPaidAmount * 0.5;
                $refundReason = 'Hủy trước giờ chơi trên 24 tiếng: hoàn 50%.';
            } else if ($hoursUntilStart >= 12 && $hoursUntilStart <= 24) {
                // Hủy đơn trước giờ chơi từ 12-24 tiếng: hoàn 25% số tiền đã thanh toán
                $refundAmount = $actualPaidAmount * 0.25;
                $refundReason = 'Hủy trước giờ chơi từ 12-24 tiếng: hoàn 25%.';
            } else {
                // Hủy đơn trong vòng 12 tiếng trước giờ chơi: không hoàn tiền
                $refundAmount = 0;
                $refundReason = 'Hủy trong vòng 12 tiếng trước giờ chơi: không hoàn phí.';
            }
        }

        DB::beginTransaction();
        try {
            // Cập nhật trạng thái đơn đặt sân
            $booking->update([
                'status' => 'cancelled',
                'cancel_time' => now(),
            ]);

            // Tạo bản ghi hoàn tiền
            if ($refundAmount > 0) {
                Refund::create([
                    'customer_id' => $booking->customer_id,
                    'refund_amount' => $refundAmount,
                    'refund_reason' => $refundReason,
                    'bookable_type' => 'App\Models\SingleBooking',
                    'bookable_id' => $booking->id
                ]);

                // Cập nhật số dư ví của khách hàng
                $customer = \App\Models\User::find($booking->customer_id);
                if ($customer) {
                    $customer->wallets += $refundAmount;
                    $customer->save();
                }
            }

            // Trừ điểm thân thiết khi hủy sân (nếu không phải là chủ sân hủy)
            if (!$isOwner) {
                $customer = \App\Models\User::find($booking->customer_id);
                if ($customer && $customer->point > 0) {
                    $customer->point = max(0, $customer->point - 1); // Trừ 1 điểm nhưng không để âm
                    $customer->save();
                }
            }

            DB::commit();

            $successMessage = 'Đơn đặt sân đã được hủy thành công!';
            if ($refundAmount > 0) {
                $successMessage .= ' Số tiền hoàn trả là ' . number_format($refundAmount) . ' Xu';
            }

            // Thêm thông báo về việc trừ điểm thân thiết
            if (!$isOwner && $customer && $customer->point >= 0) {
                $successMessage .= ' Chúng ta đã mất đi 1 điểm thân thiết.';
            }

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

    //Hủy đơn đặt sân định kỳ
    public function cancelSubscriptionBooking(Request $request, $id)
    {
        $booking = SubscriptionBooking::findOrFail($id);
        $user = Auth::user();
        $isOwner = $user->role === 'owner';

        // Kiểm tra quyền hủy đơn
        if (!$isOwner && $booking->customer_id !== $user->id) {
            return back()->with('error', 'Bạn không có quyền hủy đơn đặt sân định kỳ này!');
        }

        // Kiểm tra trạng thái đơn
        if ($booking->status == 'cancelled' || $booking->status == 'completed') {
            $message = $isOwner
                ? 'Không thể hủy đơn đặt sân định kỳ đã hủy hoặc đã hoàn thành!'
                : 'Đơn đặt sân định kỳ đã hủy hoặc đã hoàn thành, không thể hủy lại!';

            $redirectRoute = $isOwner
                ? route('owner.bookings.subscription', $booking->id)
                : route('booking.subscription.detail', $booking->id);

            return redirect($redirectRoute)->with('error', $message);
        }

        // Tính số tiền thực tế đã thanh toán (đặt cọc hoặc thanh toán đầy đủ)
        $actualPaidAmount = $booking->payment_type === 'deposit' 
            ? $booking->total_price * 0.5 
            : $booking->total_price;

        // Xử lý dữ liệu hoàn tiền
        if ($isOwner) {
            // Chủ sân tự nhập lý do và số tiền hoàn trả
            $data = $request->validate([
                'refund_amount' => 'required|numeric|min:0|max:' . $actualPaidAmount,
                'refund_reason' => 'required|string|max:255',
            ]);

            $refundAmount = $data['refund_amount'];
            $refundReason = $data['refund_reason'];
        } else {
            // Người dùng: lý do và số tiền tự động theo trường hợp
            $now = now();
            $startDate = \Carbon\Carbon::parse($booking->start_date);
            $bookingCreatedTime = \Carbon\Carbon::parse($booking->created_at);
            $minutesSinceBooking = $now->diffInMinutes($bookingCreatedTime);
            $hoursUntilStart = $now->diffInHours($startDate, false);

            if ($minutesSinceBooking <= 5) {
                // Hoàn 100% số tiền đã thanh toán
                $refundAmount = $actualPaidAmount;
                $refundReason = 'Hủy trong vòng 5 phút sau khi đặt: hoàn 100%.';
            } else if ($hoursUntilStart > 24) {
                // Hoàn 50% số tiền đã thanh toán
                $refundAmount = $actualPaidAmount * 0.5;
                $refundReason = 'Hủy trước ngày chơi đầu tiên trên 24 tiếng: hoàn 50%';
            } else if ($hoursUntilStart >= 12 && $hoursUntilStart <= 24) {
                // Hoàn 25% số tiền đã thanh toán
                $refundAmount = $actualPaidAmount * 0.25;
                $refundReason = 'Hủy trước ngày chơi đầu tiên từ 12-24 tiếng: hoàn 25%.';
            } else {
                // Không hoàn tiền
                $refundAmount = 0;
                $refundReason = 'Hủy trước ngày chơi đầu tiên dưới 12 tiếng: không hoàn phí.';
            }
        }

        DB::beginTransaction();
        try {
            // Cập nhật trạng thái đơn đặt sân
            $booking->update([
                'status' => 'cancelled',
                'cancel_time' => now(),
            ]);

            // Tạo bản ghi hoàn tiền
            if ($refundAmount > 0) {
                Refund::create([
                    'customer_id' => $booking->customer_id,
                    'refund_amount' => $refundAmount,
                    'refund_reason' => $refundReason,
                    'bookable_type' => 'App\Models\SubscriptionBooking',
                    'bookable_id' => $booking->id
                ]);

                // Cập nhật số dư ví của khách hàng
                $customer = \App\Models\User::find($booking->customer_id);
                if ($customer) {
                    $customer->wallets += $refundAmount;
                    $customer->save();
                }
            }

            // Trừ điểm thân thiết khi hủy sân định kỳ (nếu không phải là chủ sân hủy)
            if (!$isOwner) {
                $customer = \App\Models\User::find($booking->customer_id);
                if ($customer && $customer->point > 0) {
                    $customer->point = max(0, $customer->point - 5); // Trừ 5 điểm nhưng không để âm
                    $customer->save();
                }
            }

            DB::commit();

            $successMessage = 'Đơn đặt sân đã được hủy thành công!';
            if ($refundAmount > 0) {
                $successMessage .= ' Số tiền hoàn trả là ' . number_format($refundAmount) . ' Xu.';
            }

            // Thêm thông báo về việc trừ điểm thân thiết
            if (!$isOwner && $customer && $customer->point >= 0) {
                $successMessage .= ' Chúng ta đã mất đi 5 điểm thân thiết.';
            }

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
