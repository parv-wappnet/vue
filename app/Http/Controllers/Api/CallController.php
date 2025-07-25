<?php

namespace App\Http\Controllers\Api;

use App\Events\CallInitiated;
use App\Events\CallAnswered;
use App\Events\CallRejected;
use App\Events\ICECandidateReceived;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CallController extends Controller
{
    public function initiate(Request $request)
    {
        $request->validate([
            'to' => 'required|integer|exists:users,id',
            'offer' => 'required',
        ]);
        \Log::info('User ID: ' . Auth::id() . ', Requested ID: ' . $request->to);
        broadcast(new CallInitiated(Auth::user(), $request->recipient_id, $request->sdp, $request->conversation_id))->toOthers();
        return response()->json(['status' => 'call initiated']);
    }

    public function answer(Request $request)
    {
        broadcast(new CallAnswered(Auth::user(), $request->caller_id, $request->sdp))->toOthers();
        return response()->json(['status' => 'call answered']);
    }

    public function reject(Request $request)
    {
        broadcast(new CallRejected(Auth::user(), $request->caller_id))->toOthers();
        return response()->json(['status' => 'call rejected']);
    }

    public function iceCandidate(Request $request)
    {
        broadcast(new ICECandidateReceived(Auth::user(), $request->user_id, $request->candidate))->toOthers();
        return response()->json(['status' => 'candidate sent']);
    }
}
