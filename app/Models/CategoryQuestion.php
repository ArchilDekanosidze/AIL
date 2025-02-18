<?php

namespace App\Models;

use App\Models\User;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryQuestion extends Model
{
    use HasFactory, NodeTrait;

    protected $fillable = ["name", "parent_id", "depth"];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, "user_category_question")
        ->withPivot('is_active','level','target_level', 'history', 'number_to_change_level')
        // ->where('user_category_question.is_active', true)
        ->withTimestamps();
    }

    public function allQuestion()
    {
        $categories = CategoryQuestion::with(['questions', 'descendants.questions'])->find($this->id);
        $currentQuestion = $this->questions;
        $subcatQuestions = $categories->descendants->flatMap->questions;
        $allQuestion  = $currentQuestion->concat($subcatQuestions);
        return $allQuestion;        
    }





    public function path()
    {         
        $ancestorName =  $this->ancestors()->get()->pluck("name");
        $ancestorName[] = $this->name;
        return implode(" -> ", $ancestorName->toArray());
    }







}
