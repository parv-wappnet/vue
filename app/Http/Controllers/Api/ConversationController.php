<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConversationRequest;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\UserResource;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ConversationController extends Controller
{
    /**
     * Display a listing of the user's conversations.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $conversations = $user->conversations()
            ->with(['latestMessage.user', 'users'])
            ->withCount('messages')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return response()->json([
            'data' => ConversationResource::collection($conversations),
            'meta' => [
                'current_page' => $conversations->currentPage(),
                'last_page' => $conversations->lastPage(),
                'per_page' => $conversations->perPage(),
                'total' => $conversations->total(),
            ],
        ]);
    }

    /**
     * Store a newly created conversation.
     */
    public function store(StoreConversationRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // For private conversations, check if it already exists
        if ($data['type'] === 'private' && count($data['user_ids']) === 1) {
            $existingConversation = $user->conversations()
                ->where('type', 'private')
                ->whereHas('users', function ($query) use ($data) {
                    $query->whereIn('users.id', $data['user_ids']);
                })
                ->first();

            if ($existingConversation) {
                return response()->json([
                    'data' => new ConversationResource($existingConversation->load(['users', 'latestMessage'])),
                ], 200);
            }
        }

        // Create new conversation
        $conversation = Conversation::create([
            'name' => $data['name'] ?? null,
            'type' => $data['type'],
            'description' => $data['description'] ?? null,
            'created_by' => $user->id,
        ]);

        // Add users to conversation
        $userIds = array_merge($data['user_ids'], [$user->id]);
        $conversation->users()->attach($userIds, [
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        return response()->json([
            'data' => new ConversationResource($conversation->load(['users', 'creator'])),
            'message' => 'Conversation created successfully',
        ], 201);
    }

    /**
     * Display the specified conversation.
     */
    public function show(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        // Check if user is part of the conversation
        if (!$conversation->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Conversation not found'], 404);
        }

        return response()->json([
            'data' => new ConversationResource($conversation->load(['users', 'creator', 'latestMessage'])),
        ]);
    }

    /**
     * Update the specified conversation.
     */
    public function update(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        // Check if user is admin of the conversation
        $userRole = $conversation->users()->where('user_id', $user->id)->first()->pivot->role;

        if ($userRole !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $conversation->update($request->only(['name', 'description']));

        return response()->json([
            'data' => new ConversationResource($conversation->load(['users', 'creator'])),
            'message' => 'Conversation updated successfully',
        ]);
    }

    /**
     * Remove the specified conversation.
     */
    public function destroy(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();

        // Check if user is admin of the conversation
        $userRole = $conversation->users()->where('user_id', $user->id)->first()->pivot->role;

        if ($userRole !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $conversation->delete();

        return response()->json(['message' => 'Conversation deleted successfully']);
    }

    /**
     * Get users available for starting a conversation.
     */
    public function availableUsers(Request $request): JsonResponse
    {
        $user = $request->user();

        $users = User::where('id', '!=', $user->id)
            ->select(['id', 'name', 'email', 'avatar', 'status', 'last_seen_at'])
            ->get();

        return response()->json([
            'data' => UserResource::collection($users),
        ]);
    }
}
