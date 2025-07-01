<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FollowController;
use Illuminate\Http\Client\Request;
// use Illuminate\Routing\Route;

use Illuminate\Support\Facades\Route; // ✅ This is REQUIRED

// use Illuminate\Support\Facades\Broadcast;    

// Broadcast::routes(['middleware' => ['auth:sanctum']]);
Route::get('/auth/redirect/google', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/callback/google', [AuthController::class, 'handleGoogleCallback']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->post('/user/set-password', [AuthController::class, 'setPassword']);
Route::middleware('auth:sanctum')->prefix('follow')->group(function () {
    Route::get('/search', [FollowController::class, 'search']);
    Route::post('/request', [FollowController::class, 'sendRequest']);
    Route::post('/respond', [FollowController::class, 'respondRequest']);
    Route::get('/pending', [FollowController::class, 'pendingRequests']);
    Route::get('/accepted', [FollowController::class, 'accepted']);
});




Route::get('/ping', function () {
    \Log::info('Ping endpoint hit');
    return response()->json(['message' => 'pong']);
});

Route::middleware('auth:sanctum')->get('/test-auth', function () {
    return response()->json(['ok' => true]);
});
