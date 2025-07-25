<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
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

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    $conversation = Conversation::find($conversationId);
    if (!$conversation) {
        \Log::warning('Conversation not found: ' . $conversationId);
        return false; // Conversation does not exist
    }
    if ($conversation->type === 'private') {
        // For private conversations, check if the user is the creator or receiver
        if ($conversation->created_by === $user->id || $conversation->receiver_id === $user->id) {
            return true; // User is authorized for private conversation
        } else {
            \Log::warning('User is not authorized for private conversation: ' . $conversationId);
            return false; // User is not authorized for private conversation
        }
    }
    if ($conversation->type === 'group') {
        // For group conversations, check if the user is a participant
        if (in_array($user->id, $conversation->group->members)) {
            return true; // User is authorized for group conversation
        } else {
            \Log::warning('User is not authorized for group conversation: ' . $conversationId);
            return false; // User is not authorized for group conversation
        }
    }
    \Log::warning('Conversation type not recognized: ' . $conversation->type);
    return false; // Conversation type not recognized
});

Broadcast::channel('user.{id}', function ($user, $id) {
    \Log::info('for call' . $user->id . ', Requested ID: ' . $id);
    return (int) $user->id === (int) $id;
});
