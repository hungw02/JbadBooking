<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Court;
use App\Models\CourtRate;
use App\Models\SingleBooking;
use App\Models\SubscriptionBooking;
use App\Models\User;
use App\Models\Refund;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\BaseBookingController;

class SingleBookingController extends BaseBookingController
{
    // Trang đặt sân theo buổi
    public function create()
    {
        $courts = Court::where('status', 'available')->get();
        
        // Get active promotions for single booking
        $promotions = Promotion::where('status', 'active')
            ->where(function($query) {
                $query->where('booking_type', 'all')
                      ->orWhere('booking_type', 'single');
            })
            ->where('discount_percent', '>', 0) // Only get promotions with discount
            ->where(function($query) {
                $now = Carbon::now();
                $query->where(function($q) use ($now) {
                        $q->where('end_date', '>=', $now)
                          ->orWhereNull('end_date'); // Include permanent promotions
                    })
                    ->where('start_date', '<=', $now);
            })
            ->get();
        
        return view('booking.single-booking', compact('courts', 'promotions'));
    }

    // Xử lý đặt sân
    public function store(Request $request)
    {
        // Kiểm tra đăng nhập và chuyển hướng nếu chưa đăng nhập
        if (!Auth::check()) {
            // Lưu URL hiện tại vào intended để quay lại sau khi đăng nhập
            return redirect()->route('login')->with('info', 'Vui lòng đăng nhập để đặt sân');
        }

        $validator = Validator::make($request->all(), [
            'court_ids' => 'required|array',
            'court_ids.*' => 'exists:courts,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'payment_type' => 'required|in:deposit,full',
            'payment_method' => 'required|in:vnpay,wallet',
            'promotion_id' => 'nullable|exists:promotions,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Validate that all selected courts are available for the requested time
        $date = $request->date;
        $startDateTime = Carbon::parse($date . ' ' . $request->start_time);
        $endDateTime = Carbon::parse($date . ' ' . $request->end_time);

        // Prevent booking times in the past
        $now = Carbon::now();
        if ($startDateTime->isPast()) {
            return redirect()->back()->with('error', 'Không thể đặt sân cho thời gian đã qua')->withInput();
        }

        // Ensure end time is after start time (handles 00:00 as a special case)
        if ($request->end_time !== '00:00' && $startDateTime->greaterThanOrEqualTo($endDateTime)) {
            return redirect()->back()->with('error', 'Giờ kết thúc phải sau giờ bắt đầu')->withInput();
        }

        // Process promotion if available
        $discountPercent = $this->checkValidPromotion($request->promotion_id, 'single');

        // Check if courts are available
        $courtConflicts = [];

        DB::beginTransaction();
        try {
            $totalPrice = 0;
            $bookingModels = [];

            foreach ($request->court_ids as $courtId) {
                // Check for conflicts with existing bookings
                $conflicts = $this->checkConflicts($courtId, $startDateTime, $endDateTime);

                if ($conflicts) {
                    $courtConflicts[$courtId] = $conflicts;
                    continue;
                }

                // Calculate price for this court
                $price = $this->calculatePriceInternal($startDateTime, $endDateTime);
                
                // Apply discount if promotion is valid
                if ($discountPercent > 0) {
                    $discount = $price * ($discountPercent / 100);
                    $price = $price - $discount;
                }
                
                $totalPrice += $price;

                // Create booking
                $booking = SingleBooking::create([
                    'court_id' => $courtId,
                    'customer_id' => Auth::id(),
                    'start_time' => $startDateTime,
                    'end_time' => $endDateTime,
                    'payment_type' => $request->payment_type,
                    'payment_method' => $request->payment_method,
                    'total_price' => $price,
                    'status' => $request->payment_method === 'vnpay' ? 'pending' : 'confirmed',
                    'promotion_id' => $request->promotion_id,
                    'discount_percent' => $discountPercent,
                ]);
                
                $bookingModels[] = $booking;
            }

            // If there are conflicts, rollback and return with conflict information
            if (!empty($courtConflicts)) {
                DB::rollback();
                $conflictMessage = $this->formatConflictMessage($courtConflicts);
                return redirect()->back()->with('conflicts', $conflictMessage)->withInput();
            }

            // Handle payment
            $user = User::find(Auth::id());
            
            // For wallet payment
            if ($request->payment_method === 'wallet') {
                $amountToPay = $request->payment_type === 'deposit' ? $totalPrice * 0.5 : $totalPrice;
                
                if ($user->wallets < $amountToPay) {
                    DB::rollback();
                    return redirect()->back()->with('error', 'Số dư trong ví không đủ để thanh toán')->withInput();
                }
                
                $user->wallets -= $amountToPay;
                $user->save();
                
                // Cộng 1 điểm cho người dùng
                $user->point += 1;
                $user->save();
                
                DB::commit();
                return redirect()->route('booking.single.confirmation')
                    ->with('success', 'Đặt sân thành công! Bạn được cộng 1 điểm thân thiết.');
            } 
            // For VNPay payment
            else if ($request->payment_method === 'vnpay') {
                try {
                    $vnpayService = new \App\Services\VNPayService();
                    $amountToPay = $request->payment_type === 'deposit' ? $totalPrice * 0.5 : $totalPrice;
                    
                    DB::commit();
                    
                    // Redirect to VNPay payment gateway
                    return redirect()->away($vnpayService->createPaymentUrl(
                        $user->id,
                        'single',
                        $amountToPay,
                        $request->payment_type
                    ));
                } catch (\Exception $e) {
                    DB::rollback();
                    return redirect()->back()->with('error', 'Lỗi khi tạo giao dịch: ' . $e->getMessage())->withInput();
                }
            }
            
            // If we get here, something is wrong with the payment method
            DB::rollback();
            return redirect()->back()->with('error', 'Phương thức thanh toán không hợp lệ')->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage())->withInput();
        }
    }

    // Trang xác nhận đặt sân
    public function confirmation()
    {
        // Get user's latest booking (including both confirmed and cancelled)
        $bookings = SingleBooking::where('customer_id', Auth::id())
            ->whereIn('status', ['confirmed', 'cancelled'])
            ->orderBy('created_at', 'desc')
            ->take(1)  // Chỉ lấy 1 đơn mới nhất
            ->with('court')
            ->get();

        return view('booking.single-confirmation', compact('bookings'));
    }

    // Tính giá
    protected function calculatePriceInternal($startDateTime, $endDateTime)
    {
        $dayOfWeek = $startDateTime->format('N') + 1;
        $totalPrice = 0;
        $currentTime = clone $startDateTime;

        // Create a copy of endDateTime to handle midnight case
        $endTime = clone $endDateTime;
        if ($endTime->format('H:i') === '00:00') {
            $endTime->addDay(); // Add a day to correctly calculate if end time is midnight
        }

        while ($currentTime < $endTime) {
            // Move forward in 30-minute intervals or less
            $nextTime = (clone $currentTime)->addMinutes(30);
            if ($nextTime > $endTime) {
                $nextTime = clone $endTime;
            }

            // Get rate for current time slot
            $rate = CourtRate::where('day_of_week', $dayOfWeek)
                ->where('start_time', '<=', $currentTime->format('H:i:s'))
                ->where('end_time', '>=', $currentTime->format('H:i:s'))
                ->first();

            if ($rate) {
                // Calculate hours for this time slot (as a fraction)
                $hourFraction = $nextTime->diffInMinutes($currentTime) / 60;
                $totalPrice += $rate->price_per_hour * $hourFraction;
            }

            $currentTime = $nextTime;
        }

        // Ensure the price is always positive
        return abs($totalPrice);
    }

    // Định dạng thông báo xung đột
    protected function formatConflictMessage($conflicts)
    {
        $message = '';
        foreach ($conflicts as $courtId => $conflictBookings) {
            $court = Court::find($courtId);
            $message .= "Sân {$court->name} đã được đặt trong các khoảng thời gian: <br>";

            foreach ($conflictBookings as $booking) {
                if (isset($booking['type']) && $booking['type'] === 'single') {
                    $message .= "- " . Carbon::parse($booking['start_time'])->format('H:i') . " - " .
                        Carbon::parse($booking['end_time'])->format('H:i') . " ngày " .
                        Carbon::parse($booking['start_time'])->format('d/m/Y') . "<br>";
                } else {
                    $message .= "- " . Carbon::parse($booking['start_time'])->format('H:i') . " - " .
                        Carbon::parse($booking['end_time'])->format('H:i') . " (đặt định kỳ)<br>";
                }
            }

            $message .= "<br>";
        }

        return $message;
    }

    // Hàm xử lý để lấy khuyến mãi hợp lệ
    public function getValidPromotions(Request $request)
    {
        // Get active promotions for single booking
        $promotions = Promotion::where('status', 'active')
            ->where(function($query) {
                $query->where('booking_type', 'all')
                      ->orWhere('booking_type', 'single');
            })
            ->where('discount_percent', '>', 0) // Only get promotions with discount
            ->where(function($query) {
                $now = Carbon::now();
                $query->where(function($q) use ($now) {
                        $q->where('end_date', '>=', $now)
                          ->orWhereNull('end_date'); // Include permanent promotions
                    })
                    ->where('start_date', '<=', $now);
            })
            ->get();

        return response()->json([
            'success' => true,
            'promotions' => $promotions
        ]);
    }
}
