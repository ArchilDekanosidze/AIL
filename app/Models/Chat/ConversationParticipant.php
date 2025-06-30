<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConversationParticipant extends Model
{
    use HasFactory;

    protected $table = 'chat_conversation_participants';

    protected $fillable = ['conversation_id', 'user_id', 'role', 'is_muted', 'is_banned', 'last_read_message_id'];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPersianRoleAttribute()
    {
        return match ($this->role) {
            'member' => 'عضو ساده',
            'admin'   => 'مدیر',
            'super_admin' => 'مدیر کل',
            default   => 'نامشخص',
        };
    }
}
