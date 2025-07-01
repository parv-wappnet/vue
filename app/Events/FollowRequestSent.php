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

    public function broadcastOn(): array
    {
        // return [new PrivateChannel("user.{$this->receiverId}")];
        return [new PrivateChannel("follow")];
    }

    public function broadcastAs(): string
    {
        return 'follow.request';
    }
}
