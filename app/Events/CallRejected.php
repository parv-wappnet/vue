<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class CallRejected implements ShouldBroadcast
{
    use SerializesModels;

    public $receiverId;
    public $callerId;

    public function __construct($receiverId, $callerId)
    {
        $this->receiverId = $receiverId;
        $this->callerId = $callerId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel("user.{$this->callerId}");
    }

    public function broadcastWith()
    {
        return [
            'receiver_id' => $this->receiverId,
        ];
    }
}
