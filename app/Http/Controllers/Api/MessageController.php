<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // Fetch all messages for a conversation
    public function index($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        $messages = $conversation->messages()->with('sender:id,name,email')->get();
        return response()->json($messages);
    }

    // Send a message in a conversation
    public function store(Request $request, $conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
        $validated = $request->validate([
            'body' => 'required|string|max:5000',
        ]);
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'content' => $validated['body'],
        ]);
        broadcast(new MessageSent($message))->toOthers(); // Exclude sender
        return response()->json(['message' => 'Message sent', 'data' => $message], 201);
    }

    public function getOrCreatePrivateConversation(Request $request, $otherUserId)
    {

        $authUser = $request->user();
        $conversation = Conversation::where('type', 'private')
            ->where(function ($query) use ($authUser, $otherUserId) {
                $query->where('created_by', $authUser->id)->where('receiver_id', $otherUserId);
            })
            ->orWhere(function ($query) use ($authUser, $otherUserId) {
                $query->where('created_by', $otherUserId)->where('receiver_id', $authUser->id);
            })
            ->first();
        if (!$conversation) {
            $conversation = Conversation::create([
                'type' => 'private',
                'created_by' => $authUser->id,
                'receiver_id' => $otherUserId,
            ]);
        }
        if ($conversation->type == 'private') {
            $conversation_name = $conversation->creator->id == $otherUserId ? $conversation->creator->name : $conversation->receiver->name;
        } else if ($conversation->type == 'group') {
            $conversation_name = $conversation->group->name;
        } else {
            $conversation_name = 'Conversation';
        }

        return response()->json(['conversation_id' => $conversation->id, 'name' => $conversation_name, 'type' => $conversation->type]);
    }
}
