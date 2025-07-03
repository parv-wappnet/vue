<?php

namespace App\Http\Controllers\Api;

use App\Events\FollowRequestAccepted;
use App\Http\Controllers\Controller;
use App\Models\FollowRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Events\FollowRequestSent;

class FollowController extends Controller
{
    public function search(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();
        return $user ? response()->json($user) : response()->json(['message' => 'User not found'], 404);
    }

    public function sendRequest(Request $request)
    {
        $data = $request->validate(['receiver_id' => 'required|exists:users,id']);
        $follow = FollowRequest::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $data['receiver_id'],
        ]);
        // Dispatch follow request event
        // \Log::info("broadcasting follow request from user ID: {$request->user()->id} to receiver ID: {$data['receiver_id']}");
        event(new FollowRequestSent($request->user(), $data['receiver_id']));
        // broadcast(new FollowRequestSent($request->user(), $data['receiver_id']))->toOthers();

        return response()->json(['message' => 'Follow request sent', 'follow' => $follow]);
    }

    public function respondRequest(Request $request)
    {
        $data = $request->validate([
            'request_id' => 'required|exists:follow_requests,id',
            'status' => 'required|in:accepted,rejected',
        ]);

        $follow = FollowRequest::find($data['request_id']);
        if ($follow->receiver_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $follow->update(['status' => $data['status']]);
        if ($request->status === 'accepted') {
            event(new FollowRequestAccepted($follow->sender, $follow->receiver));
        }
        return response()->json(['message' => 'Response recorded']);
    }

    public function pendingRequests(Request $request)
    {
        $user = $request->user();
        $requests = FollowRequest::where('receiver_id', $user->id)->where('status', 'pending')->with('sender')->get();
        return response()->json($requests);
    }

    public function accepted(Request $request)
    {
        $user = $request->user();

        // Find all accepted follow requests where user is sender or receiver
        $acceptedRequests = FollowRequest::where('status', 'accepted')
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->latest()
            ->get();

        // Collect the connected user (not the authenticated one)
        $connectedUsers = $acceptedRequests->map(function ($req) use ($user) {
            return $req->sender_id === $user->id ? $req->receiver : $req->sender;
        });

        return response()->json($connectedUsers->unique('id')->values());
    }
}
