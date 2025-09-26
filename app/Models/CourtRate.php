<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class CourtRate extends Model
{
    protected $fillable = [
        'day_of_week',
        'start_time',
        'end_time',
        'price_per_hour'
    ];

    public function getDayNameAttribute()
    {
        return self::getDayNameStatic($this->day_of_week);
    }
    
    /**
     * Phương thức tĩnh để lấy tên ngày từ số ngày
     *
     * @param int $dayOfWeek Số ngày (2-8)
     * @return string Tên ngày
     */
    public static function getDayNameStatic($dayOfWeek)
    {
        return match($dayOfWeek) {
            2 => 'Thứ 2',
            3 => 'Thứ 3',
            4 => 'Thứ 4',
            5 => 'Thứ 5',
            6 => 'Thứ 6',
            7 => 'Thứ 7',
            8 => 'Chủ nhật',
            default => 'Không xác định'
        };
    }
    
    /**
     * Scope lọc theo ngày trong tuần
     */
    public function scopeForDayOfWeek($query, $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }
    
    /**
     * Scope tìm giá áp dụng cho thời điểm cụ thể
     */
    public function scopeForTimeSlot($query, $timeString)
    {
        return $query->where('start_time', '<=', $timeString)
                     ->where('end_time', '>=', $timeString);
    }
    
    /**
     * Scope tìm tất cả giá cho một khoảng thời gian
     */
    public function scopeOverlappingTimeRange($query, $startTime, $endTime)
    {
        return $query->where(function($q) use ($startTime, $endTime) {
            $q->where(function($innerQ) use ($startTime, $endTime) {
                $innerQ->where('start_time', '>=', $startTime)
                       ->where('start_time', '<', $endTime);
            })->orWhere(function($innerQ) use ($startTime, $endTime) {
                $innerQ->where('end_time', '>', $startTime)
                       ->where('end_time', '<=', $endTime);
            })->orWhere(function($innerQ) use ($startTime, $endTime) {
                $innerQ->where('start_time', '<=', $startTime)
                       ->where('end_time', '>=', $endTime);
            });
        });
    }
    
    /**
     * Scope lấy giá theo thứ tự tăng dần
     */
    public function scopeOrderByDayAndTime($query)
    {
        return $query->orderBy('day_of_week')->orderBy('start_time');
    }
}
