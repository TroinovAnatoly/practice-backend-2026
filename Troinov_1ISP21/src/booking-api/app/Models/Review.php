<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Booking;
use App\Models\Resource;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'resource_id',
        'user_id',
        'rating',
        'comment',
    ];

    // Связь с пользователем
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Связь с бронированием
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}