<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    /**
     * Display a listing of messages for a conversation.
     */
    public function index(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        // Check if user is part of the conversation
        if (!$conversation->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Conversation not found'], 404);
        }

        $messages = $conversation->messages()
            ->with('user')
            ->notDeleted()
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json([
            'data' => MessageResource::collection($messages),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ],
        ]);
    }

    /**
     * Store a newly created message.
     */
    public function store(StoreMessageRequest $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Check if user is part of the conversation
        if (!$conversation->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Conversation not found'], 404);
        }

        $messageData = [
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'content' => $data['content'] ?? '',
            'type' => $data['type'],
        ];

        // Handle file upload if present
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('chat-files', $fileName, 'public');

            $messageData['file_url'] = Storage::url($filePath);
            $messageData['file_name'] = $file->getClientOriginalName();
            $messageData['file_size'] = $file->getSize();
        } elseif (isset($data['file_url'])) {
            $messageData['file_url'] = $data['file_url'];
            $messageData['file_name'] = $data['file_name'] ?? null;
            $messageData['file_size'] = $data['file_size'] ?? null;
        }

        $message = Message::create($messageData);

        // Update conversation's updated_at timestamp
        $conversation->touch();

        // Broadcast message to other users in the conversation
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'data' => new MessageResource($message->load('user')),
            'message' => 'Message sent successfully',
        ], 201);
    }

    /**
     * Display the specified message.
     */
    public function show(Request $request, Conversation $conversation, Message $message): JsonResponse
    {
        $user = $request->user();

        // Check if user is part of the conversation
        if (!$conversation->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Conversation not found'], 404);
        }

        // Check if message belongs to the conversation
        if ($message->conversation_id !== $conversation->id) {
            return response()->json(['message' => 'Message not found'], 404);
        }

        return response()->json([
            'data' => new MessageResource($message->load('user')),
        ]);
    }

    /**
     * Update the specified message.
     */
    public function update(Request $request, Conversation $conversation, Message $message): JsonResponse
    {
        $user = $request->user();

        // Check if user is part of the conversation
        if (!$conversation->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Conversation not found'], 404);
        }

        // Check if message belongs to the conversation and user
        if ($message->conversation_id !== $conversation->id || $message->user_id !== $user->id) {
            return response()->json(['message' => 'Message not found'], 404);
        }

        $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        $message->update([
            'content' => $request->content,
        ]);

        $message->markAsEdited();

        return response()->json([
            'data' => new MessageResource($message->load('user')),
            'message' => 'Message updated successfully',
        ]);
    }

    /**
     * Remove the specified message.
     */
    public function destroy(Request $request, Conversation $conversation, Message $message): JsonResponse
    {
        $user = $request->user();

        // Check if user is part of the conversation
        if (!$conversation->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Conversation not found'], 404);
        }

        // Check if message belongs to the conversation and user
        if ($message->conversation_id !== $conversation->id || $message->user_id !== $user->id) {
            return response()->json(['message' => 'Message not found'], 404);
        }

        $message->softDelete();

        return response()->json(['message' => 'Message deleted successfully']);
    }
}
