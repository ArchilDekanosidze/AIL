<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TestController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $parentCategoryId = 1;
        $OriginalParentCategory = CategoryQuestion::find($parentCategoryId);
        $allCategoriesId = CategoryQuestion::withDepth()->where('parent_id', $parentCategoryId)->get()->sortBy('_lft')->pluck("id")->toArray();
        $userCategories = $user->categoryQuestions()->whereIn("category_question_id", $allCategoriesId)->get()->sortBy('lft');
        $ids = $userCategories->pluck('id')->toArray();
        $labels = $userCategories->pluck('name')->toArray();
        $levels = $userCategories->pluck('pivot.level')->toArray();
        $target_levels = $userCategories->pluck('pivot.target_level')->toArray();
        $level_history = $userCategories->pluck('pivot.level_history')->toArray();
        $level_history_time = $userCategories->pluck('pivot.level_history_time')->toArray();

        dd($userCategories);
    }
 
}
