<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class CallAnswered implements ShouldBroadcast
{
    use SerializesModels;

    public $receiver;
    public $callerId;
    public $sdp;

    public function __construct($receiver, $callerId, $sdp)
    {
        $this->receiver = $receiver;
        $this->callerId = $callerId;
        $this->sdp = $sdp;
    }

    public function broadcastOn()
    {
        return new PrivateChannel("user.{$this->callerId}");
    }

    public function broadcastWith()
    {
        return [
            'receiver' => $this->receiver,
            'sdp' => $this->sdp,
        ];
    }
}
