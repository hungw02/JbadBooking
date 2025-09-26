<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'image',
        'maintenance_start_date',
        'maintenance_end_date'
    ];

    public function singleBookings()
    {
        return $this->hasMany(SingleBooking::class);
    }

    public function subscriptionBookings()
    {
        return $this->hasMany(SubscriptionBooking::class);
    }
}
