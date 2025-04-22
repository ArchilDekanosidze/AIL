<?php
namespace App\Http\Controllers\Desktop;



use App\Models\User;
use App\Services\Quiz\QuizService;
use App\Http\Controllers\Controller;

class QuizListController extends Controller
{
    private $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;

    }


    public function quizList(User $user)
    {
        $this->preCheckQuizList(); 
        $quizzes =  $user->quizzes;
        $quizzes = $quizzes->sortByDesc("started_at");
        return view('desktop.quizList.index', compact('quizzes'));
    }

    public function preCheckQuizList()
    {
        $this->quizService->checkForEndedQuiz();
    }

}
