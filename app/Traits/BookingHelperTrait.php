<?php

namespace App\Traits;

use App\Models\CourtRate;
use Carbon\Carbon;

trait BookingHelperTrait
{
    /**
     * Lấy tên ngày trong tuần từ day_of_week
     * 
     * @return string
     */
    public function getDayOfWeekName()
    {
        return CourtRate::getDayNameStatic($this->day_of_week);
    }
    
    /**
     * Tính tổng giờ của booking
     * 
     * @return float
     */
    public function getTotalHoursAttribute()
    {
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        
        // Xử lý trường hợp thời gian kết thúc là 00:00 (đêm)
        if ($end->format('H:i') === '00:00') {
            $end->addDay();
        }
        
        return $end->diffInMinutes($start) / 60;
    }
    
    /**
     * Kiểm tra xem booking còn hiệu lực không
     * 
     * @return bool
     */
    public function isActive()
    {
        return $this->status === 'confirmed';
    }
    
    /**
     * Kiểm tra xem booking đã hoàn thành chưa
     * 
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }
    
    /**
     * Kiểm tra xem booking đã bị hủy chưa
     * 
     * @return bool
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }
    
    /**
     * Format số tiền với định dạng tiền tệ
     * 
     * @param int $amount
     * @return string
     */
    public static function formatCurrency($amount)
    {
        return number_format($amount) . ' đ';
    }
} 