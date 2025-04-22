<?php

namespace App\Models;

use App\Models\User;
use App\Models\FreeQuestion;
use App\Models\FreeQuestionCommentVote;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FreeQuestionComment extends Model
{
    use HasFactory;

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function freeQuestion() {
        return $this->belongsTo(FreeQuestion::class);
    }

    public function parent()
    {
        return $this->belongsTo(FreeQuestionComment::class, 'parent_id');
    }

    public function freeQuestionCommentVotes() {
        return $this->hasMany(FreeQuestionCommentVote::class);
    }
}
