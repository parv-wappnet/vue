<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Client\Request;
// use Illuminate\Routing\Route;

use Illuminate\Support\Facades\Route; // ✅ This is REQUIRED


Route::get('/auth/redirect/google', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/callback/google', [AuthController::class, 'handleGoogleCallback']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->post('/user/set-password', [AuthController::class, 'setPassword']);




Route::get('/ping', function () {
    \Log::info('Ping endpoint hit');
    return response()->json(['message' => 'pong']);
});
