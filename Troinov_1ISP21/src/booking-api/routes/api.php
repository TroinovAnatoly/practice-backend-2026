<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ResourceController;
use App\Http\Controllers\Api\BookingController;

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function(){

    // Resources
    Route::get('/resources',[ResourceController::class,'index']);
    Route::post('/resources',[ResourceController::class,'store']);
    Route::put('/resources/{id}',[ResourceController::class,'update']);
    Route::delete('/resources/{id}',[ResourceController::class,'destroy']);

    // Bookings
    Route::post('/bookings',[BookingController::class,'store']);
    Route::get('/bookings/{id}',[BookingController::class,'show']);
    Route::post('/bookings/{id}/cancel',[BookingController::class,'cancel']);

});