<?php

namespace App\Models;

use App\Models\User;
use App\Models\FreeTag;
use App\Models\FreeQuestionVote;
use App\Models\FreeQuestionComment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FreeQuestion extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function freeQuestionVotes() {
        return $this->hasMany(FreeQuestionVote::class);
    }

    public function freeQuestionComments() {
        return $this->hasMany(FreeQuestionComment::class);
    }

    public function freeTags()
    {
        return $this->belongsToMany(FreeTag::class);
    }

}
