<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param int    $messageId      The ID of the message that was deleted.
     * @param int    $conversationId The ID of the conversation the message belonged to.
     * @param string $deletionType   'for_everyone' or 'for_me'.
     * @param int    $userId         The ID of the user who performed the deletion.
     */
    public function __construct(
        public int $messageId,
        public int $conversationId,
        public string $deletionType,
        public int $userId
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|\Illuminate\Broadcasting\PrivateChannel
     */
    public function broadcastOn()
    {
        if ($this->deletionType === 'for_me') {
            return new PrivateChannel('user.' . $this->userId); // User-specific private channel
        }

        return new PrivateChannel('chat.conversation.' . $this->conversationId); // Conversation-specific private channel
    }

    /**
     * The name of the event to broadcast.
     * This will be the name listened for on the frontend (e.g., `.listen('.MessageDeleted', ...)`)
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'MessageDeleted';
    }

    /**
     * Get the data to broadcast.
     * This data will be available as `e.messageId`, `e.deletionType`, etc. in your JavaScript listener.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'messageId' => $this->messageId,
            'conversationId' => $this->conversationId,
            'deletionType' => $this->deletionType,
            'userId' => $this->userId,
        ];
    }
}