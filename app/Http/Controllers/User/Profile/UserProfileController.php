<?php

namespace App\Http\Controllers\User\Profile;

use App\Models\User;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use App\Services\Quiz\QuizService;
use App\Http\Controllers\Controller;

class UserProfileController extends Controller
{
    private $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;

    }

    
    public function index()
    {
       return view("user.profile.profile");
    }

    public function chooseCategory()
    {       
        $this->quizService->checkForEndedQuiz();
        $user = auth()->user();
        $userCategories = $user->categoryQuestions()->get()->sortBy('lft');
        $allCategories = CategoryQuestion::withDepth()->get()->sortBy('_lft')->skip(1);
       return view('user.learning.new.chooseCategory', compact('userCategories', 'allCategories'));
    }

    public function quizList()
    {
        $this->quizService->checkForEndedQuiz();
        $user = auth()->user();
        $quizzes =  $user->quizzes;
        // dd($quizzes->first()->persianStatus);
        return view('user.learning.Quiz.QuizList', compact('quizzes'));
    }
}
