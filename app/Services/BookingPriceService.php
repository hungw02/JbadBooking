<?php

namespace App\Services;

use App\Models\CourtRate;
use App\Models\Promotion;
use Carbon\Carbon;

class BookingPriceService
{
    /**
     * Tính tổng giá cho khoảng thời gian
     *
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @return float
     */
    public function calculatePrice(Carbon $startDateTime, Carbon $endDateTime)
    {
        // Xác định ngày trong tuần (2-8)
        $dayOfWeek = (int)$startDateTime->format('N') + 1; // Chuyển từ 1-7 (Thứ 2-CN) sang 2-8
        $totalPrice = 0;
        $currentTime = clone $startDateTime;

        // Xử lý trường hợp thời gian kết thúc là 00:00
        $endTime = clone $endDateTime;
        if ($endTime->format('H:i') === '00:00') {
            $endTime->addDay();
        }

        while ($currentTime < $endTime) {
            // Di chuyển theo từng khoảng 30 phút
            $nextTime = (clone $currentTime)->addMinutes(30);
            if ($nextTime > $endTime) {
                $nextTime = clone $endTime;
            }

            // Lấy giá cho khoảng thời gian hiện tại
            $rate = CourtRate::where('day_of_week', $dayOfWeek)
                ->where('start_time', '<=', $currentTime->format('H:i:s'))
                ->where('end_time', '>=', $currentTime->format('H:i:s'))
                ->first();

            if ($rate) {
                // Tính phần giờ (dưới dạng phân số)
                $hourFraction = $nextTime->diffInMinutes($currentTime) / 60;
                $price = $rate->price_per_hour * $hourFraction;
                $totalPrice += $price;
            }

            $currentTime = $nextTime;
        }
        
        return abs($totalPrice);
    }

    /**
     * Tính giá đã áp dụng khuyến mãi
     *
     * @param float $totalPrice
     * @param int|null $promotionId
     * @param string $bookingType 'single' hoặc 'subscription'
     * @return float
     */
    public function applyPromotionDiscount($totalPrice, $promotionId, $bookingType)
    {
        if (!$promotionId) {
            return $totalPrice;
        }

        $promotion = Promotion::find($promotionId);
        
        if (!$promotion || $promotion->status !== 'active') {
            return $totalPrice;
        }
        
        if ($promotion->booking_type !== 'all' && $promotion->booking_type !== $bookingType) {
            return $totalPrice;
        }
        
        $now = Carbon::now();
        
        if ($promotion->start_date && $promotion->start_date > $now) {
            return $totalPrice;
        }
        
        if ($promotion->end_date && $promotion->end_date < $now) {
            return $totalPrice;
        }
        
        // Áp dụng giảm giá
        $discountAmount = $totalPrice * ($promotion->discount_percent / 100);
        return $totalPrice - $discountAmount;
    }
    
    /**
     * Tính giá cho đặt sân định kỳ
     * 
     * @param string $startTime
     * @param string $endTime
     * @param int $dayOfWeek
     * @param string $startDate
     * @param string $endDate
     * @param int $courtCount
     * @param int|null $promotionId
     * @return array Mảng chứa thông tin giá [raw_price, discount, final_price]
     */
    public function calculateSubscriptionPrice(
        $startTime, 
        $endTime, 
        $dayOfWeek, 
        $startDate, 
        $endDate, 
        $courtCount = 1,
        $promotionId = null
    ) {
        // Tính giá mỗi buổi
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        $pricePerSession = $this->calculatePrice($start, $end);
        
        // Tính tổng số buổi
        $sessionCount = $this->countSessions($startDate, $endDate, $dayOfWeek);
        
        // Tính tổng giá gốc (không giảm giá)
        $rawPrice = $pricePerSession * $sessionCount * $courtCount;
        
        // Áp dụng khuyến mãi nếu có
        $finalPrice = $this->applyPromotionDiscount($rawPrice, $promotionId, 'subscription');
        
        return [
            'raw_price' => $rawPrice,
            'promotion_discount' => $rawPrice - $finalPrice,
            'final_price' => $finalPrice,
            'session_count' => $sessionCount
        ];
    }
    
    /**
     * Đếm số buổi đặt sân trong khoảng thời gian và ngày của tuần
     *
     * @param string $startDate
     * @param string $endDate
     * @param int $dayOfWeek
     * @return int
     */
    public function countSessions($startDate, $endDate, $dayOfWeek)
    {
        $start = new Carbon($startDate);
        $end = new Carbon($endDate);
        $count = 0;

        // Chuyển đổi từ định dạng 2-8 sang định dạng 1-7 của PHP
        $phpWeekDay = $dayOfWeek === 8 ? 7 : $dayOfWeek;

        while ($start <= $end) {
            if ($start->format('N') == $phpWeekDay) {
                $count++;
            }
            $start->addDay();
        }

        return $count;
    }
} 