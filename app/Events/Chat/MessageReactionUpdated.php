<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReactionUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message_id;
    public $user_id;
    public $emoji;
    public $status;

    /**
     * Create a new event instance.
     */
    public function __construct($messageId, $userId, $emoji, $status)
    {
        $this->message_id = $messageId;
        $this->user_id = $userId;
        $this->emoji = $emoji;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // This line is crucial: it needs to correctly fetch the conversation ID
        // Make sure your Message model has a 'chat_conversation_id' foreign key
        // or whatever column links a message to its conversation.
        $message = \App\Models\Chat\Message::find($this->message_id);
        if ($message) {
            return [
                new PrivateChannel('chat.conversation.' . $message->conversation_id),
            ];
        }
        // Fallback or error handling if message not found (shouldn't happen if fired correctly)
        return []; 
    }

    // Optional: You can define broadcastAs if you want a different event name
    // public function broadcastAs()
    // {
    //     return 'reaction.updated'; // Then listen for .listen('.reaction.updated')
    // }


    public function broadcastWith(): array
    {
        return [
            'message_id' => $this->message_id,
            'user_id' => $this->user_id,
            'emoji' => $this->emoji,
            'status' => $this->status, // 'added', 'removed', or 'changed'
        ];
    }

    public function broadcastAs()
    {
        return 'MessageReactionUpdated';
    }
}