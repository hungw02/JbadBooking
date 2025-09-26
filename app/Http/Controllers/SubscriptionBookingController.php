<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Court;
use App\Models\CourtRate;
use App\Models\SubscriptionBooking;
use App\Models\SingleBooking;
use App\Models\User;
use App\Models\Refund;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\BaseBookingController;

class SubscriptionBookingController extends BaseBookingController
{
    public function create()
    {
        $courts = Court::all();

        // Get active promotions for subscription booking
        $promotions = Promotion::where('status', 'active')
            ->where(function ($query) {
                $query->where('booking_type', 'all')
                    ->orWhere('booking_type', 'subscription');
            })
            ->where('discount_percent', '>', 0) // Only get promotions with discount
            ->where(function ($query) {
                $now = Carbon::now();
                $query->where(function ($q) use ($now) {
                    $q->where('end_date', '>=', $now)
                        ->orWhereNull('end_date'); // Include permanent promotions
                })
                    ->where('start_date', '<=', $now);
            })
            ->get();

        return view('booking.subscription-booking', compact('courts', 'promotions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'court_ids' => 'required|array',
            'court_ids.*' => 'exists:courts,id',
            'day_of_week' => 'required|integer|between:2,8',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'payment_type' => 'required|in:deposit,full',
            'payment_method' => 'required|in:vnpay,wallet',
            'promotion_id' => 'nullable|exists:promotions,id'
        ]);

        // Nếu là xác nhận từ form chọn sân thay thế, tiếp tục mà không cần kiểm tra xung đột
        $checkForConflicts = !($request->has('confirm_alternatives') && $request->confirm_alternatives === 'true');

        // Check for existing bookings with the same courts, day of week, and overlapping times
        $conflicts = [];
        $startTime = $request->start_time . ':00';
        $endTime = $request->end_time . ':00';

        $currentDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Tìm tất cả các sân còn trống trong khung giờ này
        $bookedCourts = [];
        $conflictingCourts = [];

        if ($checkForConflicts) {
            // Find the first occurrence of the selected day of week in the date range
            while ($currentDate <= $endDate) {
                if ((int)$currentDate->format('N') + 1 == $request->day_of_week) {
                    break;
                }
                $currentDate->addDay();
            }

            // If we found a date matching the day of week
            if ($currentDate <= $endDate) {
                $date = $currentDate->format('Y-m-d');

                foreach ($request->court_ids as $courtId) {
                    // Check for overlapping subscription bookings
                    $existingBookings = SubscriptionBooking::where('court_id', $courtId)
                        ->where('day_of_week', $request->day_of_week)
                        ->where('status', 'confirmed')
                        ->where(function ($query) use ($request) {
                            $query->where('start_date', '<=', $request->end_date)
                                ->where('end_date', '>=', $request->start_date);
                        })
                        ->where(function ($query) use ($startTime, $endTime) {
                            $query->where(function ($q) use ($startTime, $endTime) {
                                // Booking starts during our timeframe
                                $q->where('start_time', '>=', $startTime)
                                    ->where('start_time', '<', $endTime);
                            })
                                ->orWhere(function ($q) use ($startTime, $endTime) {
                                    // Booking ends during our timeframe
                                    $q->where('end_time', '>', $startTime)
                                        ->where('end_time', '<=', $endTime);
                                })
                                ->orWhere(function ($q) use ($startTime, $endTime) {
                                    // Booking completely encompasses our timeframe
                                    $q->where('start_time', '<=', $startTime)
                                        ->where('end_time', '>=', $endTime);
                                });
                        })
                        ->get();

                    if ($existingBookings->count() > 0) {
                        $court = Court::find($courtId);

                        $conflictInfo = [
                            'court_id' => $courtId,
                            'court_name' => $court->name,
                            'day' => $this->getDayName($request->day_of_week),
                            'start_time' => Carbon::parse($existingBookings->first()->start_time)->format('H:i'),
                            'end_time' => Carbon::parse($existingBookings->first()->end_time)->format('H:i'),
                        ];

                        $conflictInfo['message'] = "Sân {$court->name}: Đã có người đặt vào {$this->getDayName($request->day_of_week)} từ {$conflictInfo['start_time']} đến {$conflictInfo['end_time']}.";

                        $conflicts[] = $conflictInfo;
                        $conflictingCourts[] = $courtId;
                    }

                    $bookedCourts[] = $courtId;
                }

                if (count($conflicts) > 0) {
                    // Tìm tất cả các sân còn trống trong khung giờ này
                    $availableCourts = $this->findAvailableCourts(
                        $request->day_of_week,
                        $startTime,
                        $endTime,
                        $request->start_date,
                        $request->end_date,
                        $bookedCourts
                    );

                    // Lưu thông tin conflicts và sân khả dụng vào session
                    $messages = [];
                    foreach ($conflicts as $conflict) {
                        $messages[] = $conflict['message'];
                    }

                    return back()
                        ->with('conflicts', implode('<br>', $messages))
                        ->with('conflict_day', $this->getDayName($request->day_of_week))
                        ->with('conflict_time', Carbon::parse($startTime)->format('H:i') . ' - ' . Carbon::parse($endTime)->format('H:i'))
                        ->with('has_alternatives', count($availableCourts) > 0)
                        ->with('available_courts', $availableCourts)
                        ->with('conflicting_courts', $conflictingCourts)
                        ->with('start_date', $request->start_date)
                        ->with('end_date', $request->end_date)
                        ->with('day_of_week', $request->day_of_week)
                        ->with('start_time', $request->start_time)
                        ->with('end_time', $request->end_time)
                        ->with('payment_type', $request->payment_type)
                        ->with('payment_method', $request->payment_method);
                }
            }
        }

        // Process promotion if available
        $discountPercent = $this->checkValidPromotion($request->promotion_id, 'subscription');

        // Calculate total price
        $pricePerSession = $this->calculatePricePerSession(
            $request->start_time,
            $request->end_time,
            $request->day_of_week
        );

        $sessionCount = $this->countSessions(
            $request->start_date,
            $request->end_date,
            $request->day_of_week
        );

        // Calculate total original price based on number of courts
        $originalPrice = $pricePerSession * $sessionCount * count($request->court_ids);
        
        // Calculate final price after discount
        $finalPrice = $originalPrice;
        if ($discountPercent > 0) {
            $discount = $originalPrice * ($discountPercent / 100);
            $finalPrice = $originalPrice - $discount;
        }

        // Calculate payment amount based on payment type
        $paymentAmount = $request->payment_type === 'deposit' ? $finalPrice * 0.5 : $finalPrice;

        try {
            DB::beginTransaction();

            // Create a booking record for each selected court
            $bookingIds = [];
            
            // Tính giá cho từng sân riêng biệt sau khi đã giảm giá
            $finalPricePerCourt = $finalPrice / count($request->court_ids);
            
            foreach ($request->court_ids as $courtId) {
                $booking = SubscriptionBooking::create([
                    'customer_id' => Auth::id(),
                    'court_id' => $courtId,
                    'day_of_week' => $request->day_of_week,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'total_price' => $finalPricePerCourt,
                    'payment_type' => $request->payment_type,
                    'payment_method' => $request->payment_method,
                    'status' => $request->payment_method === 'vnpay' ? 'pending' : 'confirmed',
                    'promotion_id' => $request->promotion_id,
                    'discount_percent' => $discountPercent
                ]);
                $bookingIds[] = $booking->id;
            }

            // Handle payment based on method
            if ($request->payment_method === 'wallet') {
                // Check wallet balance
                $user = User::find(Auth::id());
                if ($user->wallets < $paymentAmount) {
                    return back()->with('error', 'Số dư trong ví không đủ để thanh toán');
                }
                
                // Deduct from wallet
                $user->wallets -= $paymentAmount;
                $user->save();
                
                // Cộng 5 điểm cho người dùng khi đặt sân thành công
                $user->point += 5;
                $user->save();
                
                DB::commit();
                
                return redirect()->route('booking.subscription.confirmation')
                    ->with('success', 'Đặt sân định kỳ thành công! Bạn được cộng 5 điểm thân thiết.');
            } else if ($request->payment_method === 'vnpay') {
                // For VNPay, use the VNPay service
                $vnpayService = new \App\Services\VNPayService();
                
                // Create payment URL
                $paymentUrl = $vnpayService->createPaymentUrl(
                    Auth::id(),
                    'subscription',
                    $paymentAmount,
                    $request->payment_type
                );
                
                // Mark bookings as pending payment
                foreach (SubscriptionBooking::whereIn('id', $bookingIds)->get() as $booking) {
                    // No need to change status as we've already created with confirmed status
                }
                
                DB::commit();
                
                // Redirect to VNPay
                return redirect()->away($paymentUrl);
            }

            DB::commit();

            // Cộng 5 điểm cho người dùng khi đặt sân thành công
            $user = User::find(Auth::id());
            $user->point += 5;
            $user->save();

            return redirect()->route('booking.subscription.confirmation')
                ->with('success', 'Đặt sân định kỳ thành công! Bạn được cộng 5 điểm thân thiết.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription Booking Error: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra khi đặt sân. Vui lòng thử lại.');
        }
    }

    public function confirmation()
    {
        $bookings = SubscriptionBooking::where('customer_id', Auth::id())
            ->whereIn('status', ['confirmed', 'cancelled'])
            ->with('court')
            ->get();

        if ($bookings->isEmpty()) {
            return redirect()->route('booking.subscription.create');
        }

        $subscription = $bookings->first(); // Get the latest subscription
        return view('booking.subscription-confirmation', compact('subscription', 'bookings'));
    }

    private function calculatePricePerSession($startTime, $endTime, $dayOfWeek)
    {
        $priceService = app(\App\Services\BookingPriceService::class);
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        return $priceService->calculatePrice($start, $end);
    }

    protected function countSessions($startDate, $endDate, $dayOfWeek)
    {
        $priceService = app(\App\Services\BookingPriceService::class);
        return $priceService->countSessions($startDate, $endDate, $dayOfWeek);
    }

    protected function getDayName($dayOfWeek)
    {
        // Sử dụng phương thức tĩnh từ model CourtRate
        return CourtRate::getDayNameStatic($dayOfWeek);
    }

    protected function findAvailableCourts($dayOfWeek, $startTime, $endTime, $startDate, $endDate, $excludeCourtIds = [])
    {
        $conflictService = app(\App\Services\BookingConflictService::class);
        return $conflictService->findAvailableCourtsForSubscription(
            $dayOfWeek,
            $startTime,
            $endTime,
            $startDate,
            $endDate,
            $excludeCourtIds
        );
    }

    public function getValidPromotions(Request $request)
    {
        // Get active promotions for subscription booking
        $promotions = Promotion::where('status', 'active')
            ->where(function ($query) {
                $query->where('booking_type', 'all')
                    ->orWhere('booking_type', 'subscription');
            })
            ->where('discount_percent', '>', 0) // Only get promotions with discount
            ->where(function ($query) {
                $now = Carbon::now();
                $query->where(function ($q) use ($now) {
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
