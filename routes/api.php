<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\BookingController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class,'login']);
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

Route::apiResource('events', EventController::class)->middleware('auth:sanctum');
Route::apiResource('bookings', BookingController::class)->middleware('auth:sanctum');