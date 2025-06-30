<?php

namespace App\Http\Middleware;

use App\Events\UserStatusChanged;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Update user status if authenticated
        if ($request->user()) {
            $user = $request->user();
            $oldStatus = $user->status;

            // Update status to online and last seen
            $user->update([
                'status' => 'online',
                'last_seen_at' => now(),
            ]);

            // Broadcast status change if it changed
            if ($oldStatus !== 'online') {
                broadcast(new UserStatusChanged($user, 'online'));
            }
        }

        return $response;
    }
}
