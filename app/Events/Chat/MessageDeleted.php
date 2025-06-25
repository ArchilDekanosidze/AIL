<?php

namespace App\Events\Chat;

use App\Models\Chat\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

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
            // Private channel for user-specific deletion notification
            return new PrivateChannel('user.' . $this->userId);
        }

        // Load conversation to check type
        $conversation = Conversation::find($this->conversationId);

        if ($conversation && $conversation->type !== 'private') {
            // Public channel for non-private conversation deletion (visible to guests)
            return new Channel('chat.conversation.' . $this->conversationId);
        }

        // Default: private channel for private conversations
        return new PrivateChannel('chat.conversation.' . $this->conversationId);
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