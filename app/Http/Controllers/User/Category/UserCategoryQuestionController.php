<?php

namespace App\Http\Controllers\User\Category;

use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class UserCategoryQuestionController extends Controller
{

    public function index(CategoryQuestion $category)
    {                   
        Auth::loginUsingId(1, TRUE);        
        $currentCategory = $category;
        $directCats =  $category->children()->get();
        $ancestor = $currentCategory->ancestors()->get();         

        return view('user.category.question.index', compact('currentCategory', 'directCats', 'ancestor'));
    }
}
