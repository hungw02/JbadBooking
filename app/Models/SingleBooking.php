<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BookingHelperTrait;

class SingleBooking extends Model
{
    use HasFactory, BookingHelperTrait;

    protected $fillable = [
        'court_id',
        'customer_id',
        'start_time',
        'end_time',
        'payment_type',
        'payment_method',
        'total_price',
        'status',
        'cancel_time',
        'promotion_id',
        'discount_percent'
    ];

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function refunds()
    {
        return $this->morphMany(Refund::class, 'bookable');
    }

    public function storages()
    {
        return $this->morphMany(Storage::class, 'bookable');
    }

    // Tính Tổng giá trước khi áp dụng khuyến mãi
    public function getOriginalPrice()
    {
        if ($this->discount_percent > 0) {
            // Tính ngược Tổng giá từ giá đã giảm và phần trăm giảm giá
            return round($this->total_price / (1 - $this->discount_percent / 100));
        }
        
        return $this->total_price;
    }
    
    // Tính số tiền được giảm
    public function getDiscountAmount()
    {
        if ($this->discount_percent > 0) {
            return $this->getOriginalPrice() - $this->total_price;
        }
        
        return 0;
    }
}
