<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class FollowRequestSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sender;
    public $receiverId;

    public function __construct(User $sender, int $receiverId)
    {
        $this->sender = $sender;
        $this->receiverId = $receiverId;
    }
    // broadcastOn() — Defines the channel the event will be broadcasted on.

    // broadcastAs() — Customizes the event name for frontend listeners.

    // broadcastWith() — Customizes the data payload sent to the frontend
    public function broadcastOn(): array
    {
        // return [new PrivateChannel("user.{$this->receiverId}")];
        // \Log::info('🔊 Broadcasting follow request from: ' . $this->sender->id . ' to: ' . $this->receiverId);

        return ["follow"];
    }

    public function broadcastAs(): string
    {
        return 'follow-request';
    }
}
