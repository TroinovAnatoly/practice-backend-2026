<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'capacity',
        'floor',
        'has_projector',
        'has_whiteboard'
    ];

    public function bookings() {
    return $this->hasMany(Booking::class);
    }
    public function reviews() {
        return $this->hasManyThrough(Review::class, Booking::class);
    }
}