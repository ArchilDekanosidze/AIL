<?php

namespace App\Models;

use App\Models\ExamTag;
use App\Models\CategoryExam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_exam_id',
        'title',
        'url',
        'has_answer',
        'school_type',
        'state',
        'city',
    ];

    public function categoryExam()
    {
        return $this->belongsTo(CategoryExam::class);
    }

    public function tags()
    {
        return $this->hasMany(ExamTag::class);
    }
}
