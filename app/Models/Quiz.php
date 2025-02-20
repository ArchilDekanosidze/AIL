<?php

namespace App\Models;

use App\Models\User;
use App\Models\QuizQuestion;
use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quiz extends Model
{
    use HasFactory;




    public function users()
    {
        return $this->belongsToMany(User::class , "user_quiz");      
    }

    public function quizQuestions()
    {
        return $this->hasMany(QuizQuestion::class);
    }

    public function getPersianStatusAttribute()
    {
        return [
            "ended" => "تمام شده",
            "running" => "در حال اجرا",
            "created" => 'ساخته شده'
        ][$this->status];
    }
    public function getCreatedAtAttribute()
    {
        $value = $this->attributes['created_at'];
        $time = new Verta($value);
        return $time->formatDifference();

    }

    protected $casts = [
        'history' => 'array',
    ];

}
