<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'chat_messages'; // ✅ Fixes the table name

     protected $fillable = [
        'conversation_id',
        'sender_id',
        'content',
        'reply_to_message_id',
        'deleted_for_user_ids'
    ];

    protected $casts = [
        'edited_at' => 'datetime', // Cast edited_at to a datetime object
        'deleted_at' => 'datetime', // Cast deleted_at to a datetime object for soft deletes
        'deleted_for_user_ids' => 'array', // <-- THIS IS THE CRITICAL LINE
    ];


    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class);
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_id');
    }
}
