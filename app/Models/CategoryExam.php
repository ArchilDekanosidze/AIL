<?php

namespace App\Models;

use App\Models\Exam;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryExam extends Model
{
    use HasFactory , NodeTrait, SoftDeletes;
    protected $fillable = ['name', 'url',  'parent_id'];

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}
