<?php
namespace App\Events\Chat;


use App\Models\Chat\Message;
use App\Models\Chat\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

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
        // Access conversation_id from messageData array, not from $this->message
        $conversationId = $this->messageData['conversation_id'] ?? null;

        if (!$conversationId) {
            // If no conversation_id, fallback or throw error
            return [];
        }

        $conversation = Conversation::find($conversationId);

        if ($conversation && $conversation->type !== 'private') {
            // Public channel for group/channel
            return [new Channel('chat.conversation.' . $conversation->id)];
        }

        // Private channel for private conversation
        return [new PrivateChannel('chat.conversation.' . $conversationId)];
    }

    public function broadcastWith()
    {
        return [ 'message' => $this->messageData ];
    }
}
