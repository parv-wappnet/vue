<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/ping', function () {
    \Log::info('Ping endpoint hit at ' . now()->toDateTimeString());
    return response()->json(['message' => 'pong']);
});
Route::post('/ping', function () {
    \Log::info('Ping endpoint hit at ' . now()->toDateTimeString());
    return Auth::check();
});

// Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Google OAuth Routes
Route::prefix('auth')->group(function () {
    Route::get('/redirect/google', [AuthController::class, 'redirectToGoogle']);
    Route::get('/callback/google', [AuthController::class, 'handleGoogleCallback']);
});

// Protected Routes under v1 prefix
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

    // Conversation Routes
    // Route::prefix('conversations')->group(function () {
    //     Route::get('/', [ConversationController::class, 'index']);
    //     Route::post('/', [ConversationController::class, 'store']);
    //     Route::get('/{conversation}', [ConversationController::class, 'show']);
    //     Route::put('/{conversation}', [ConversationController::class, 'update']);
    //     Route::delete('/{conversation}', [ConversationController::class, 'destroy']);
    //     Route::get('/available-users', [ConversationController::class, 'availableUsers']);
    // });


    // Message Routes
    Route::get('conversations/{id}/messages', [MessageController::class, 'index']);
    Route::post('conversations/{id}/messages', [MessageController::class, 'store']);
    Route::get('/conversations/private/{userId}', [MessageController::class, 'getOrCreatePrivateConversation']);


    // Group Routes
    Route::apiResource('groups', GroupController::class)->only(['index', 'store', 'show']);
    Route::post('groups/{id}/members', [GroupController::class, 'addMembers']);
});
