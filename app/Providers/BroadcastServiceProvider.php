<?php

namespace App\Providers;

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Broadcast::routes(['middleware' => ['auth:sanctum']]);
        require base_path('routes/channels.php');
        // Authorize private conversation channels
        Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
            $conversation = Conversation::find($conversationId);

            if (!$conversation) {
                return false;
            }

            // Check if user is part of the conversation
            return $conversation->users()->where('user_id', $user->id)->exists();
        });

        // Authorize user status channel
        Broadcast::channel('user-status', function ($user) {
            return true; // All authenticated users can listen to user status changes
        });
    }
}
