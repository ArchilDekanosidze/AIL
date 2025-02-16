<?php

namespace App\Http\Controllers\User\Profile;

use App\Models\CategoryQuestion;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class UserProfileController extends Controller
{
    public function index()
    {
       return view("user.profile.profile");
    }

    public function chooseCategory()
    {       
        $user = auth()->user();
        $userCategories = $user->categoryQuestions()->get()->sortBy('lft');
        $allCategories = CategoryQuestion::withDepth()->get()->sortBy('_lft')->skip(1);
       return view('user.learning.new.chooseCategory', compact('userCategories', 'allCategories'));
    }

    public function quizList()
    {
        $user = auth()->user();
        $quizzes =  $user->quizzes;
        // dd($quizzes->first()->persianStatus);
        return view('user.learning.Quiz.QuizList', compact('quizzes'));
    }
}
