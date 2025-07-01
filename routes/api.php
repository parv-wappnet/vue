<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FollowController;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Google OAuth Routes
Route::prefix('auth')->group(function () {
    Route::get('/redirect/google', [AuthController::class, 'redirectToGoogle']);
    Route::get('/callback/google', [AuthController::class, 'handleGoogleCallback']);
});

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // User Routes
    Route::get('/me', function (Request $request) {
        return $request->user();
    });
    Route::post('/user/set-password', [AuthController::class, 'setPassword']);

    // Follow Routes
    Route::prefix('follow')->group(function () {
        Route::get('/search', [FollowController::class, 'search']);
        Route::post('/request', [FollowController::class, 'sendRequest']);
        Route::post('/respond', [FollowController::class, 'respondRequest']);
        Route::get('/pending', [FollowController::class, 'pendingRequests']);
        Route::get('/accepted', [FollowController::class, 'accepted']);
    });
});


// Health Check
Route::get('/ping', function () {
    \Log::info('Ping endpoint hit');
    return response()->json(['message' => 'pong']);
});
