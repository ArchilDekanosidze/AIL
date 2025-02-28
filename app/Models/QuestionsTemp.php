<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionsTemp extends Model
{
    use HasFactory;
    protected $fillable = [
        "category_question_id",
        "front",
        "back",
        "p1",
        "p2",
        "p3",
        "p4",
        "answer",
        "percentage",
        "count"];

    public function category()
    {
        return $this->belongsTo(CategoryQuestion::class, 'category_question_question');
    }

    public function scopeTest($query)
    {
        return $query->where('type', 'test');
    }
    public function scopeDescriptive($query)
    {
        return $query->where('type', 'description');
    }
}
