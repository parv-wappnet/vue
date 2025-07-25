<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class CallInitiated implements ShouldBroadcast
{
    use SerializesModels;

    public $caller;
    public $receiverId;
    public $sdp;
    public $conversationId;

    public function __construct($caller, $receiverId, $sdp, $conversationId)
    {
        $this->caller = $caller;
        $this->receiverId = $receiverId;
        $this->sdp = $sdp;
        $this->conversationId = $conversationId;
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel("user.{$this->conversationId}")];
    }
    public function broadcastAs(): string
    {
        return 'CallAnswered';
    }
    public function broadcastWith(): array
    {
        return [
            'caller' => $this->caller,
            'sdp' => $this->sdp,
        ];
    }
}
