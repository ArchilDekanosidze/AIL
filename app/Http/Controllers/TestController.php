<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use Illuminate\Support\Facades\Hash;

class TestController extends Controller
{
    public function index()
    {
        $user = User::find(1);
        $selectedCategories = $user->categoryQuestions;
        $ancestorCategories = CategoryQuestion::whereHas('descendants', function ($query) use($selectedCategories){
            $query->whereIn('id', $selectedCategories->pluck('id'));
        })->get();
        $categories = $ancestorCategories->merge($selectedCategories)->unique('id')->sortBy('lft');
        dd($categories);
    }
 
}
