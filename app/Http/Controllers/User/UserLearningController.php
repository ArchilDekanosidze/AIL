<?php

namespace App\Http\Controllers\User;

use App\Models\CategoryQuestion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class UserLearningController extends Controller
{

    public function chooseCategory()
    {       
        $user = auth()->user();
        $userCategories = $user->UserCheckedCategory();
        $allCategories = CategoryQuestion::withDepth()->get()->sortBy('_lft')->skip(1);
        // dd($mainCategory->children()->get());
        // dd($allCategories->sortBy('_lft')->pluck('name'));
       return view('user.learning.chooseCategory', compact('userCategories', 'allCategories'));
    }
}
