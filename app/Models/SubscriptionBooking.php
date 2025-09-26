<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BookingHelperTrait;

class SubscriptionBooking extends Model
{
    use BookingHelperTrait;

    protected $fillable = [
        'id',
        'customer_id',
        'court_id',
        'day_of_week',
        'start_time',
        'end_time',
        'start_date',
        'end_date',
        'payment_type',
        'payment_method',
        'total_price',
        'status',
        'cancel_time',
        'promotion_id',
        'discount_percent'
    ];

    // Cho phép ID tùy chỉnh
    public $incrementing = false;

    // Tự động bật/tắt auto-incrementing dựa vào ID
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            // Nếu ID được set thủ công thì tắt auto-increment
            $model->incrementing = !isset($model->attributes['id']);
        });
    }

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_price' => 'integer',
        'day_of_week' => 'integer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }

    public function refunds()
    {
        return $this->morphMany(Refund::class, 'bookable');
    }

    public function storages()
    {
        return $this->morphMany(Storage::class, 'bookable');
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function getOriginalPrice()
    {
        if ($this->discount_percent > 0) {
            return round($this->total_price / (1 - $this->discount_percent / 100));
        }
        
        return $this->total_price;
    }

    public function getDiscountAmount()
    {
        if ($this->discount_percent > 0) {
            return $this->getOriginalPrice() - $this->total_price;
        }
        
        return 0;
    }
    
    /**
     * Tính giá gốc cho mỗi buổi đặt
     */
    public function getOriginalPricePerSession()
    {
        // Đếm số buổi
        $startDate = \Carbon\Carbon::parse($this->start_date);
        $endDate = \Carbon\Carbon::parse($this->end_date);
        $dayOfWeek = $this->day_of_week;
        
        // Đếm số buổi thực tế
        $sessionCount = 0;
        $date = clone $startDate;
        while ($date <= $endDate) {
            if ((int)$date->format('N') + 1 == $dayOfWeek) {
                $sessionCount++;
            }
            $date->addDay();
        }
        
        // Tính giá mỗi buổi
        if ($sessionCount > 0) {
            return round($this->getOriginalPrice() / $sessionCount);
        }
        
        return 0;
    }
}
