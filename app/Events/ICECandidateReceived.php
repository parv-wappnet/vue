<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ICECandidateReceived implements ShouldBroadcast
{
    use SerializesModels;

    public $senderId;
    public $receiverId;
    public $candidate;

    public function __construct($senderId, $receiverId, $candidate)
    {
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->candidate = $candidate;
    }

    public function broadcastOn()
    {
        return new PrivateChannel("user.{$this->receiverId}");
    }

    public function broadcastAs(): string
    {
        return 'ICECandidateReceived';
    }
    public function broadcastWith()
    {
        return [
            'sender_id' => $this->senderId,
            'candidate' => $this->candidate,
        ];
    }
}
