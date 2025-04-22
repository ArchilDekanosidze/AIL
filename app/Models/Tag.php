<?php

namespace App\Models;

use App\Models\User;
use App\Models\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    public function questions()
    {
        return $this->hasMany(Question::class);
    }


    public function users() 
    {
        return $this->belongsToMany(User::class, 'user_badge', 'tag_id', 'user_id')
                    ->withPivot('badge', 'score')
                    ->withTimestamps();
    }
}
