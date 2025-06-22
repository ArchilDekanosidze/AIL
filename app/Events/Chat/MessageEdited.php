<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageEdited implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param array $messageData An array containing the updated message data,
     * including 'conversation_id' and 'id' of the message.
     */
    public function __construct(
        public array $messageData
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\PrivateChannel
     */
    public function broadcastOn()
    {
        // Broadcast on a private channel specific to the conversation.
        // Ensure $this->messageData['conversation_id'] is present in the array.
        return new PrivateChannel('chat.conversation.' . $this->messageData['conversation_id']);
    }

    /**
     * The name of the event to broadcast.
     * This will be the name listened for on the frontend (e.g., `.listen('.MessageEdited', ...)`)
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'MessageEdited';
    }

    /**
     * Get the data to broadcast.
     * This data will be available as `e.message` in your JavaScript listener.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'message' => $this->messageData // Send the full updated message data
        ];
    }
}