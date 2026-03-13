<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ResourceController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ReviewController;

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function(){

    // Resources
    Route::get('/resources',[ResourceController::class,'index']);
    Route::post('/resources',[ResourceController::class,'store']);
    Route::put('/resources/{id}',[ResourceController::class,'update']);
    Route::get('/resources/{id}', [ResourceController::class, 'show']);
    Route::delete('/resources/{id}',[ResourceController::class,'destroy']);

    // Bookings
    Route::post('/bookings',[BookingController::class,'store']);
    Route::get('/bookings/{id}',[BookingController::class,'show']);
    Route::post('/bookings/{id}/cancel',[BookingController::class,'cancel']);

    // Reviews
    Route::get('/reviews',[ReviewController::class,'index']);
    Route::post('/reviews',[ReviewController::class,'store']);
    Route::get('/reviews/{id}',[ReviewController::class,'show']);
    Route::put('/reviews/{id}',[ReviewController::class,'update']);
    Route::delete('/reviews/{id}',[ReviewController::class,'destroy']);

});