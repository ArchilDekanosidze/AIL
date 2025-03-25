<?php

namespace App\Models;

use App\Models\User;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Support\Facades\Cache;
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

    public function allQuestionCount()
    {
        $categoryIds  = CategoryQuestion::descendantsAndSelf($this->id)->pluck('id');
        $questionCount = Question::whereIn('category_question_id', $categoryIds)->count();
        return $questionCount;
    }



    public function path()
    {         
        $ancestorName =  $this->ancestors()->get()->pluck("name");
        $ancestorName[] = $this->name;
        return implode(" -> ", $ancestorName->toArray());
    }

    protected static function boot()
    {
        parent::boot();
        static::saved(function(){
            self::clearCache();
        });

        static::deleted(function(){
            self::clearCache();
        });
    }

    public static function getCachedAllCategories()
    {
        return Cache::rememberForever('allCategoriesWithCountAndDepth', function () {
            return self::withDepth()->withCount('descendants')->get()->sortBy('_lft')->skip(1);
        });
    }

    public static function getCachedAllCategoryIdsWithSubcategories()
    {
        return Cache::rememberForever('allCategoryIdsWithSubcategories', function () {
            return self::whereHas('descendants')->pluck('id')->toArray();
        });
    }

    public static function clearCache()
    {
        Cache::forget('allCategoriesWithCountAndDepth');
        Cache::forget('allCategoryIdsWithSubcategories');
    }




}
