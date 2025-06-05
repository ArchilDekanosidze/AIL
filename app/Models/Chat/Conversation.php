<?php

namespace App\Models\Chat;

use App\Models\User;
use App\Models\Chat\Message;
use Illuminate\Database\Eloquent\Model;
use App\Models\Chat\ConversationParticipant;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;
    
    protected $table = 'chat_conversations';

    protected $fillable = ['type', 'title', 'created_by'];

    public function participants()
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }
}
