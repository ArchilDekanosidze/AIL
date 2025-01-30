<?php

namespace App\Http\Controllers\User;

use App\Models\CategoryQuestion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class UserQuestionController extends Controller
{

    public function getRandomQuestion(Request $request)
    {       
        $currentCategoryId = $request->currentCategoryId;
        $category = CategoryQuestion::find($currentCategoryId);        
        $randomQuestion = $category->randomQuestion();              
        return $randomQuestion;
    }

    public function add_category_to_user(Request $request)
    {
        $user = auth()->user();
        $currentCategoryId = $request->currentCategoryId;
        $categoriesId = CategoryQuestion::descendantsAndSelf($currentCategoryId)->pluck('id');
        $user->categoryQuestions()->attach($categoriesId);
        return $currentCategoryId;
    }

    public function remove_category_from_user(Request $request)
    {
        $user = auth()->user();
        $currentCategoryId = $request->currentCategoryId;
        $categoriesId = CategoryQuestion::descendantsAndSelf($currentCategoryId)->pluck('id');
        $user->categoryQuestions()->detach($categoriesId);
        return $currentCategoryId;
    }
}
