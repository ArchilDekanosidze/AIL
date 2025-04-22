<?php

namespace App\Models;

use App\Models\User;
use App\Models\FreeQuestion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FreeQuestionVote extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'user_id',
        'free_question_id' , 
        'value'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function freeQuestion() {
        return $this->belongsTo(FreeQuestion::class);
    }
}
