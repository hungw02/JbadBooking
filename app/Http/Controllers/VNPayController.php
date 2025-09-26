<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VNPayService;
use App\Models\SingleBooking;
use App\Models\SubscriptionBooking;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class VNPayController extends Controller
{
    /**
     * Handle VNPay payment return
     */
    public function paymentReturn(Request $request)
    {
        $vnpayService = new VNPayService();
        $result = $vnpayService->validatePayment($request->all());
        
        // Ghi log kết quả
        Log::info('VNPay payment return', [
            'result' => $result,
            'request' => $request->all()
        ]);
        
        // Kiểm tra kết quả và xử lý theo loại booking
        $bookingType = $result['data']['booking_type'] ?? '';
        $userId = $result['data']['user_id'] ?? 0;
        
        if ($result['success']) {
            // Cập nhật trạng thái đơn đặt sân từ pending thành confirmed
            $this->updateBookingStatus($bookingType, $userId, 'confirmed');
            
            // Ghi nhận giao dịch
            $this->recordTransaction($result);
            
            // Redirect về trang xác nhận tùy theo loại booking
            if ($bookingType === 'single') {
                return redirect()->route('booking.single.confirmation')
                    ->with('success', 'Thanh toán thành công! Bạn được cộng 1 điểm thân thiết.');
            } elseif ($bookingType === 'subscription') {
                return redirect()->route('booking.subscription.confirmation')
                    ->with('success', 'Thanh toán thành công! Bạn được cộng 5 điểm thân thiết.');
            }
        } else {
            // Người dùng đã hủy thanh toán hoặc thanh toán thất bại
            // Xóa đơn đặt sân có trạng thái pending
            $this->deleteBookings($bookingType, $userId);
            
            // Nếu thanh toán thất bại, chuyển hướng đến trang booking với thông báo
            if ($bookingType === 'single') {
                return redirect()->route('booking.single.create')
                    ->with('error', 'Bạn đã hủy thanh toán. Lịch đặt đã được hủy');
            } elseif ($bookingType === 'subscription') {
                return redirect()->route('booking.subscription.create')
                    ->with('error', 'Bạn đã hủy thanh toán. Lịch đặt đã được hủy');
            }
        }
        
        // Nếu không nhận diện được loại booking, về trang chủ
        return redirect()->route('home')
            ->with('error', $result['message']);
    }
    
    /**
     * Update booking status
     */
    private function updateBookingStatus($bookingType, $userId, $status)
    {
        try {
            if ($bookingType === 'single') {
                SingleBooking::where('customer_id', $userId)
                    ->where('status', 'pending')
                    ->where('payment_method', 'vnpay')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->update(['status' => $status]);
                    
                // Cộng điểm khi thanh toán thành công
                if ($status === 'confirmed') {
                    $user = User::find($userId);
                    if ($user) {
                        $user->point += 1;
                        $user->save();
                    }
                }
            } elseif ($bookingType === 'subscription') {
                SubscriptionBooking::where('customer_id', $userId)
                    ->where('status', 'pending')
                    ->where('payment_method', 'vnpay')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->update(['status' => $status]);
                    
                // Cộng điểm khi thanh toán thành công
                if ($status === 'confirmed') {
                    $user = User::find($userId);
                    if ($user) {
                        $user->point += 5;
                        $user->save();
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to update booking status', [
                'error' => $e->getMessage(),
                'booking_type' => $bookingType,
                'user_id' => $userId
            ]);
        }
    }
    
    /**
     * Delete pending bookings when payment is cancelled
     */
    private function deleteBookings($bookingType, $userId)
    {
        try {
            if ($bookingType === 'single') {
                SingleBooking::where('customer_id', $userId)
                    ->where('status', 'pending')
                    ->where('payment_method', 'vnpay')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->delete();
            } elseif ($bookingType === 'subscription') {
                SubscriptionBooking::where('customer_id', $userId)
                    ->where('status', 'pending')
                    ->where('payment_method', 'vnpay')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->delete();
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete pending bookings', [
                'error' => $e->getMessage(),
                'booking_type' => $bookingType,
                'user_id' => $userId
            ]);
        }
    }
    
    /**
     * Record payment transaction
     */
    private function recordTransaction($result)
    {
        try {
            $paymentData = $result['data'];
            
            // Instead of using PaymentTransaction model, directly log transaction details
            Log::info('Payment transaction recorded', [
                'user_id' => $paymentData['user_id'],
                'booking_type' => $paymentData['booking_type'],
                'amount' => $paymentData['amount'],
                'transaction_id' => $paymentData['transaction_id'],
                'bank_code' => $paymentData['bank_code'],
                'status' => $paymentData['transaction_status'],
                'date' => $paymentData['transaction_date'],
                'method' => 'vnpay'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to record transaction', [
                'error' => $e->getMessage(),
                'data' => $result
            ]);
        }
    }
} 