<?php

namespace App\Models;

use App\Models\User;
use App\Models\FreeQuestion;
use App\Models\FreeQuestionComment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FreeQuestionCommentVote extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'user_id',
        'free_question_comment_id' , 
        'value'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function freeQuestionComment() {
        return $this->belongsTo(FreeQuestionComment::class);
    }
}
