<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Promotion extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'discount_percent',
        'start_date',
        'end_date',
        'status',
        'booking_type'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function isActive()
    {
        // Kiểm tra trạng thái kích hoạt
        if ($this->status !== 'active') {
            return false;
        }

        $now = Carbon::now();
        
        // Nếu không có end_date (vĩnh viễn), chỉ cần kiểm tra sau ngày bắt đầu
        if ($this->isPermanent()) {
            return $now->gte($this->start_date->startOfDay());
        }
        
        // Kiểm tra thời gian hiện tại có nằm trong khoảng khuyến mãi
        return $now->between(
            $this->start_date->startOfDay(),
            $this->end_date->endOfDay()
        );
    }

    // Thêm các helper methods
    public function hasStarted()
    {
        return Carbon::now()->gte($this->start_date->startOfDay());
    }

    public function hasEnded()
    {
        if ($this->isPermanent()) {
            return false;
        }
        return Carbon::now()->gt($this->end_date->endOfDay());
    }

    public function getStatusText()
    {
        if ($this->status !== 'active') {
            return 'Chưa kích hoạt';
        }

        if (!$this->hasStarted()) {
            return 'Sắp diễn ra';
        }

        if ($this->hasEnded()) {
            return 'Đã kết thúc';
        }

        return 'Đang áp dụng';
    }
    
    public function isPermanent()
    {
        return $this->end_date === null;
    }
    
    public function isPromotion()
    {
        return $this->discount_percent > 0;
    }

    // Helper methods cho booking type
    public function isApplicableToSingleBooking()
    {
        return in_array($this->booking_type, ['all', 'single']);
    }

    public function isApplicableToSubscriptionBooking()
    {
        return in_array($this->booking_type, ['all', 'subscription']);
    }

    public function getBookingTypeText()
    {
        switch($this->booking_type) {
            case 'all':
                return 'Tất cả đơn đặt';
            case 'single':
                return 'Đơn đặt theo buổi';
            case 'subscription':
                return 'Đơn đặt định kỳ';
            default:
                return 'Không xác định';
        }
    }
} 