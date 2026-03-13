<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'resource_id',
        'start_time',
        'end_time',
        'status'
    ];

    // связь с ресурсом
    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    // связь с пользователем
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}