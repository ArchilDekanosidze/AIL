<?php

namespace App\Models;

use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }
    
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function question() {
        return $this->belongsTo(Question::class);
    }

    public function votes() {
        return $this->hasMany(Vote::class);
    }
    
    public function bestReply()
    {
        return $this->belongsTo(Comment::class, 'best_reply_id');
    }

    public function thread()
    {
        return $this->hasMany(Comment::class, 'original_id');
    }
}
