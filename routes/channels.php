<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::routes(['middleware' => ['auth:sanctum']]);
// Broadcast::channel('user.{id}', function ($user, $id) {
//     \Log::info("Authorizing user {$user->id} for channel user.{$id}");
//     return (int) $user->id === (int) $id;
// });
// Route::post('/broadcasting/auth', function (\Illuminate\Http\Request $request) {
//     \Log::info('🔐 Auth request received', [
//         'headers' => $request->headers->all(),
//         'user' => $request->user(),
//     ]);
//     return Broadcast::auth($request);
// })->middleware('auth:sanctum');
