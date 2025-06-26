<?php

use App\Http\Controllers\Api\AuthController;
// use Illuminate\Routing\Route;

use Illuminate\Support\Facades\Route; // ✅ This is REQUIRED


Route::get('/auth/redirect/google', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/callback/google', [AuthController::class, 'handleGoogleCallback']);
Route::get('/ping', function () {
    \Log::info('Ping endpoint hit');
    return response()->json(['message' => 'pong']);
});
