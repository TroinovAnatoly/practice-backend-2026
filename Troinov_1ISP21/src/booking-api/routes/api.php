<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ResourceController;

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function(){

    Route::get('/resources',[ResourceController::class,'index']);
    Route::post('/resources',[ResourceController::class,'store']);
    Route::put('/resources/{id}',[ResourceController::class,'update']);
    Route::delete('/resources/{id}',[ResourceController::class,'destroy']);

});