<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reaction extends Model
{
    use HasFactory;

    protected $table = 'chat_message_reactions'; // ✅ Fixes the table name

    protected $fillable = ['message_id', 'user_id', 'reaction'];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
