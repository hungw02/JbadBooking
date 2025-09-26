<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Court;
use App\Models\CourtRate;
use App\Models\SingleBooking;
use App\Models\SubscriptionBooking;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Routing\Controller as BaseController;

class BaseBookingController extends BaseController
{
    // Xem trạng thái đặt sân
    public function index()
    {
        $courts = Court::where('status', 'available')->get();
        $courtRates = $this->getTimeRangeRates();

        return view('booking.index', compact('courts', 'courtRates'));
    }

    // Kiểm tra sân trống
    public function checkAvailability(Request $request)
    {
        try {
            $date = $request->date;
            $courtId = $request->court_id;

            // Lấy danh sách đặt sân buổi đơn lẻ cho ngày và sân đã chọn
            try {
                $singleBookings = SingleBooking::where('court_id', $courtId)
                    ->whereDate('start_time', $date)
                    ->where('status', 'confirmed')
                    ->get();
            } catch (\Exception $e) {
                $singleBookings = collect();
            }

            // Lấy danh sách đặt sân theo gói thuê bao phù hợp với ngày trong tuần
            $dayOfWeekRaw = date('N', strtotime($date)); // 1 = Monday, 7 = Sunday
            $dayOfWeek = $dayOfWeekRaw + 1; // Chuyển sang định dạng 2-8 (2: Thứ 2, 8: Chủ nhật)

            // Thực hiện truy vấn lấy dữ liệu đặt sân thuê bao
            try {
                $subscriptionBookings = SubscriptionBooking::where('court_id', $courtId)
                    ->where('day_of_week', $dayOfWeek)
                    ->where('status', 'confirmed')
                    ->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date)
                    ->get();
            } catch (\Exception $e) {
                $subscriptionBookings = collect();
            }

            return response()->json([
                'single_bookings' => $singleBookings,
                'subscription_bookings' => $subscriptionBookings
            ]);
        } catch (\Exception $e) {
            // Trả về dữ liệu trống nếu xảy ra lỗi
            return response()->json([
                'single_bookings' => [],
                'subscription_bookings' => []
            ]);
        }
    }

    // Tính giá
    public function calculatePrice(Request $request)
    {
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        $date = $request->date;

        // chuyển đổi thời gian bắt đầu và kết thúc
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);

        // Xử lý trường hợp thời gian kết thúc là 00:00 (đêm)
        if ($end->format('H:i') === '00:00') {
            $end->addDay(); // Di chuyển đến ngày tiếp theo để tính toán chính xác
        }

        $totalHours = $end->diffInMinutes($start) / 60;

        // Xác định ngày trong tuần (2-8 format where 2=Monday, 8=Sunday)
        $dayOfWeek = date('N', strtotime($date)) + 1;

        // Tính giá bằng cách lặp qua từng đoạn thời gian
        $totalPrice = 0;
        $currentTime = clone $start;

        while ($currentTime < $end) {
            // Di chuyển tiếp 30 phút hoặc ít hơn (nếu đạt đến thời gian kết thúc)
            $nextHour = (clone $currentTime)->addMinutes(30);
            if ($nextHour > $end) {
                $nextHour = clone $end;
            }

            // Tìm tỷ lệ áp dụng cho đoạn thời gian hiện tại
            $rate = CourtRate::where('day_of_week', $dayOfWeek)
                ->where('start_time', '<=', $currentTime->format('H:i:s'))
                ->where('end_time', '>=', $currentTime->format('H:i:s'))
                ->first();

            if ($rate) {
                // Tính giá cho đoạn thời gian này (phần giờ * tỷ lệ giờ)
                $hourFraction = $nextHour->diffInMinutes($currentTime) / 60;
                $totalPrice += $rate->price_per_hour * $hourFraction;
            }

            // Di chuyển đến đoạn thời gian tiếp theo
            $currentTime = $nextHour;
        }

        // Đảm bảo giá là số dương
        $totalPrice = abs($totalPrice);

        return response()->json([
            'total_hours' => $totalHours,
            'total_price' => $totalPrice
        ]);
    }

    // Tính giá sử dụng nội bộ trong các controller
    protected function calculatePriceInternal($startDateTime, $endDateTime)
    {
        $priceService = app(\App\Services\BookingPriceService::class);
        return $priceService->calculatePrice(
            Carbon::parse($startDateTime),
            Carbon::parse($endDateTime)
        );
    }

    // Lấy giá theo ngày trong tuần
    public function getRatesByDay($day)
    {
        $dayOfWeek = (int)$day;
        if ($dayOfWeek < 2 || $dayOfWeek > 8) {
            return response()->json(['error' => 'Invalid day of week'], 400);
        }

        $rates = CourtRate::where('day_of_week', $dayOfWeek)
            ->orderBy('start_time')
            ->get()
            ->map(function ($rate) {
                return [
                    'start_time' => Carbon::parse($rate->start_time)->format('H:i'),
                    'end_time' => Carbon::parse($rate->end_time)->format('H:i'),
                    'price_per_hour' => $rate->price_per_hour,
                ];
            });

        return response()->json(['rates' => $rates]);
    }

    // Kiểm tra xung đột (dùng chung cho cả đặt sân đơn và định kỳ)
    protected function checkConflicts($courtId, $startDateTime, $endDateTime, $excludeBookingId = null)
    {
        $conflictService = app(\App\Services\BookingConflictService::class);
        return $conflictService->checkSingleBookingConflicts($courtId, $startDateTime, $endDateTime, $excludeBookingId);
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

    // Xử lý thanh toán
    protected function handlePayment($user, $totalPrice, $paymentMethod, $paymentType)
    {
        $amountToPay = $paymentType === 'deposit' ? $totalPrice * 0.5 : $totalPrice;

        if ($paymentMethod === 'wallet') {
            if ($user->wallets < $amountToPay) {
                return [
                    'success' => false,
                    'message' => 'Số dư trong ví không đủ để thanh toán'
                ];
            }

            $user->wallets -= $amountToPay;
            $user->save();

            return [
                'success' => true,
                'message' => 'Thanh toán bằng ví thành công'
            ];
        }

        // Xử lý thanh toán VNPay
        if ($paymentMethod === 'vnpay') {
            try {
                $vnpayService = new \App\Services\VNPayService();
                
                // Xác định loại booking từ context hiện tại
                $bookingType = get_called_class() === 'App\Http\Controllers\SingleBookingController' ? 'single' : 'subscription';
                
                // Tạo URL thanh toán VNPay
                $paymentUrl = $vnpayService->createPaymentUrl(
                    $user->id,
                    $bookingType,
                    $amountToPay,
                    $paymentType
                );
                
                // Set flash session to store booking status
                session()->flash('pending_payment', true);
                session()->flash('payment_url', $paymentUrl);
                
                // Chuyển hướng đến trang thanh toán VNPay
                return [
                    'success' => true,
                    'message' => 'Chuyển hướng đến cổng thanh toán VNPay',
                    'redirect_url' => $paymentUrl
                ];
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'message' => 'Lỗi khi tạo giao dịch: ' . $e->getMessage()
                ];
            }
        }
        
        return [
            'success' => false,
            'message' => 'Phương thức thanh toán không hợp lệ'
        ];
    }

    // Kiểm tra khuyến mãi hợp lệ
    protected function checkValidPromotion($promotionId, $bookingType)
    {
        $priceService = app(\App\Services\BookingPriceService::class);
        $discount = 0;

        if ($promotionId) {
            $promotion = Promotion::find($promotionId);
            if ($promotion) {
                // Kiểm tra nếu promotion active và phù hợp với loại booking
                if (
                    $promotion->status === 'active' &&
                    ($promotion->booking_type === 'all' || $promotion->booking_type === $bookingType)
                ) {

                    // Kiểm tra thời hạn
                    $now = Carbon::now();
                    if ((!$promotion->start_date || $promotion->start_date <= $now) &&
                        (!$promotion->end_date || $promotion->end_date >= $now)
                    ) {
                        $discount = $promotion->discount_percent;
                    }
                }
            }
        }

        return $discount;
    }

    // Tìm sân khả dụng
    protected function findAvailableCourts($dayOfWeek, $startTime, $endTime, $startDate, $endDate, $excludeCourtIds = [])
    {
        $conflictService = app(\App\Services\BookingConflictService::class);
        if ($startDate === $endDate) {
            // Đặt sân đơn lẻ
            $startDateTime = $startDate . ' ' . $startTime;
            $endDateTime = $endDate . ' ' . $endTime;
            return $conflictService->findAvailableCourts($startDateTime, $endDateTime, $excludeCourtIds);
        } else {
            // Đặt sân định kỳ
            return $conflictService->findAvailableCourtsForSubscription(
                $dayOfWeek,
                $startTime,
                $endTime,
                $startDate,
                $endDate,
                $excludeCourtIds
            );
        }
    }

    // Đếm số buổi
    protected function countSessions($startDate, $endDate, $dayOfWeek)
    {
        $priceService = app(\App\Services\BookingPriceService::class);
        return $priceService->countSessions($startDate, $endDate, $dayOfWeek);
    }

    // Lấy tên ngày trong tuần
    protected function getDayName($dayOfWeek)
    {
        // Sử dụng phương thức tĩnh từ model CourtRate
        return CourtRate::getDayNameStatic($dayOfWeek);
    }

    // Lấy giá sân theo khung giờ cho ngày thường và cuối tuần
    public function getTimeRangeRates()
    {
        $timeRanges = [
            ['05:00:00', '09:59:00'],
            ['10:00:00', '13:59:00'],
            ['14:00:00', '17:59:00'],
            ['18:00:00', '23:59:00']
        ];

        $rates = [];

        foreach ($timeRanges as $timeRange) {
            $weekdayRate = CourtRate::where('day_of_week', 2)
                ->where('start_time', $timeRange[0])
                ->where('end_time', $timeRange[1])
                ->first();

            $weekendRate = CourtRate::where('day_of_week', 7)
                ->where('start_time', $timeRange[0])
                ->where('end_time', $timeRange[1])
                ->first();

            $rates[] = [
                'time_range' => [
                    'start' => Carbon::parse($timeRange[0])->format('H:i'),
                    'end' => Carbon::parse($timeRange[1])->format('H:i')
                ],
                'weekday_price' => $weekdayRate ? $weekdayRate->price_per_hour : null,
                'weekend_price' => $weekendRate ? $weekendRate->price_per_hour : null
            ];
        }

        return $rates;
    }
}
