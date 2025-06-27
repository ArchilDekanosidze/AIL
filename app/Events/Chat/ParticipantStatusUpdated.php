<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParticipantStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversationId, $userId, $status;

    public function __construct($conversationId, $userId, $status)
    {
        $this->conversationId = $conversationId;
        $this->userId = $userId;
        $this->status = $status;
    }

    public function broadcastOn()
    {
        return new PrivateChannel("chat.{$this->userId}");
    }

    public function broadcastWith()
    {
        return [
            'conversation_id' => $this->conversationId,
            'status' => $this->status,
        ];
    }
}

