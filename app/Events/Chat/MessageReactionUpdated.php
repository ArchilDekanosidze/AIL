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
        $message = Message::find($this->message_id);

        if ($message) {
            $conversation = Conversation::find($message->conversation_id);

            if ($conversation && $conversation->type !== 'private') {
                return [new Channel('chat.conversation.' . $conversation->id)];
            }

            return [new PrivateChannel('chat.conversation.' . $conversation->id)];
        }

        return [];
    }




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