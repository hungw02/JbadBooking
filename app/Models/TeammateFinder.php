<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeammateFinder extends Model
{
    use HasFactory;

    protected $table = 'teammate_finder';

    protected $fillable = [
        'user_id',
        'full_name',
        'skill_level',
        'contact_info',
        'expectations',
        'play_time',
        'is_active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
