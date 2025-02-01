<?php

namespace App\Http\Controllers\User;

use App\Models\CategoryQuestion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class UserLearningNewController extends Controller
{

    public function chooseCategory()
    {       
        $user = auth()->user();
        $userCategories = $user->categoryQuestions()->get()->sortBy('lft');
        // dd($userCategories->first()->pivot->level);
        $allCategories = CategoryQuestion::withDepth()->get()->sortBy('_lft')->skip(1);
        // dd($userCategories);
        // dd($allCategories);
        // dd($mainCategory->children()->get());
        // dd($allCategories->sortBy('_lft')->pluck('name'));
       return view('user.learning.new.chooseCategory', compact('userCategories', 'allCategories'));
    }

    public function start(Request $request)
    {
        dd($request->all());
    }
}
