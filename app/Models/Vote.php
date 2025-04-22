<?php

namespace App\Models;

use App\Models\User;
use App\Models\Comment;
use App\Models\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vote extends Model
{
    use HasFactory;


    protected $fillable = ['user_id', 'question_id', 'answer_id', 'value'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function question() {
        return $this->belongsTo(Question::class);
    }

    public function comment() {
        return $this->belongsTo(Comment::class);
    }
}
