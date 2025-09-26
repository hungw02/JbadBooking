<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'fullname',
        'username',
        'password',
        'email',
        'phone',
        'role',
        'wallets',
        'status',
        'point'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'wallets' => 'decimal:2',
        'point' => 'integer',
    ];

    protected $attributes = [
        'role' => 'customer',
    ];

    public function singleBookings()
    {
        return $this->hasMany(SingleBooking::class, 'customer_id');
    }

    public function subscriptionBookings()
    {
        return $this->hasMany(SubscriptionBooking::class, 'customer_id');
    }

    public function imports()
    {
        return $this->hasMany(Import::class, 'owner_id');
    }

    public function getRankAttribute()
    {
        $points = $this->point;
        
        if ($points >= 1 && $points <= 5) {
            return 'Đồng';
        }
        if ($points >= 6 && $points <= 10) {
            return 'Bạc';
        }
        if ($points >= 11 && $points <= 20) {
            return 'Vàng';
        }
        if ($points >= 21 && $points <= 30) {
            return 'Bạch kim';
        }
        if ($points >= 31 && $points <= 40) {
            return 'Kim cương';
        }
        if ($points >= 41) {
            return 'Ruby';
        }
        return 'Chưa có';
    }

    public function getRankImageAttribute()
    {
        $rank = $this->rank;
        if ($rank == 'Đồng') {
            return 'bronze';
        }
        if ($rank == 'Bạc') {
            return 'silver';
        }
        if ($rank == 'Vàng') {
            return 'gold';
        }
        if ($rank == 'Bạch kim') {
            return 'platinum';
        }
        if ($rank == 'Kim cương') {
            return 'diamond';
        }
        if ($rank == 'Ruby') {
            return 'ruby';
        }
        return 'no_rank';
    }
}
