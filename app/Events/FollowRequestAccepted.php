<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use App\Models\User;

class FollowRequestAccepted implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $receiver;
    public $sender;

    public function __construct(User $sender, User $receiver)
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel("user.{$this->receiver->id}"),
            new PrivateChannel("user.{$this->sender->id}")
        ];
    }

    public function broadcastAs(): string
    {
        return 'follow-accepted';
    }

    public function broadcastWith()
    {
        return [
            'sender' => [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
                'email' => $this->sender->email,
            ]
        ];
    }
}
