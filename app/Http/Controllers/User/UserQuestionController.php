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
        $user->add_category_to_user($currentCategoryId);
        return true;
    }

    public function remove_category_from_user(Request $request)
    {
        $user = auth()->user();
        $currentCategoryId = $request->currentCategoryId;
        $user->remove_category_from_user($currentCategoryId);       
        return true;
    }
}
