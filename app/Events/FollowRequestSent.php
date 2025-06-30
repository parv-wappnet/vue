<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class FollowRequestSent implements ShouldBroadcast
{
    use SerializesModels;

    public $sender;
    public $receiverId;

    public function __construct(User $sender, int $receiverId)
    {
        $this->sender = $sender;
        $this->receiverId = $receiverId;
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel("user.{$this->receiverId}")];
    }

    public function broadcastAs(): string
    {
        return 'follow.request';
    }
}
