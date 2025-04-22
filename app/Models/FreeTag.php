<?php

namespace App\Models;

use App\Models\User;
use App\Models\FreeQuestion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FreeTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    public function freeQuestions()
    {
        return $this->belongsToMany(FreeQuestion::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_free_badge', 'free_tag_id', 'user_id')
                    ->withPivot('badge', 'score')
                    ->withTimestamps();
    }
}
