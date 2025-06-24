<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TerrainController;
use App\Http\Controllers\TerrainImageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// API Routes for Terrain Booking System
Route::prefix('api')->group(function () {
    // Terrain routes
    Route::apiResource('terrains', TerrainController::class);
    Route::get('my-terrains', [TerrainController::class, 'myTerrains']);

    // Terrain Image routes
    Route::apiResource('terrain-images', TerrainImageController::class);

    // Booking routes
    Route::apiResource('bookings', BookingController::class);
    Route::patch('bookings/{booking}/approve', [BookingController::class, 'approve']);
    Route::patch('bookings/{booking}/reject', [BookingController::class, 'reject']);
    Route::patch('bookings/{booking}/complete', [BookingController::class, 'complete']);

    // Payment routes
    Route::apiResource('payments', PaymentController::class);
    Route::patch('payments/{payment}/refund', [PaymentController::class, 'refund']);

    // Review routes
    Route::apiResource('reviews', ReviewController::class);
    Route::get('terrain-stats', [ReviewController::class, 'getTerrainStats']);

    // Favorite routes
    Route::apiResource('favorites', FavoriteController::class);
    Route::post('favorites/toggle', [FavoriteController::class, 'toggle']);
    Route::get('user-favorites', [FavoriteController::class, 'getUserFavorites']);
});