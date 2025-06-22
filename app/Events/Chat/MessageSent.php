<?php
namespace App\Events\Chat;


use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Chat\Message;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(
        public array $messageData // <-- CRITICAL CHANGE: accept an array
    ) {}

    public function broadcastAs()
    {
        return 'MessageSent';
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.conversation.' . $this->messageData['conversation_id']);
    }

    public function broadcastWith()
    {
        return [ 'message' => $this->messageData ];
    }
}
