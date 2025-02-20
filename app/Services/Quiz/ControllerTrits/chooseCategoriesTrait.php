<?php
namespace App\Services\Quiz\ControllerTrits;

use App\Models\CategoryQuestion;
use App\Services\Quiz\QuizService;



trait chooseCategoriesTrait
{

    private $quizService;
    private $user;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;

    }

    public function chooseCategories()
    {       
        $this->PreCheck();
        $this->setUser();
        $user = $this->getUser();
        return $this->returnView();
    }
    

    public function setUser()
    {
        $this->user = auth()->user();
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getRole()
    {
        return $this->user;
    }

    public function PreCheck()
    {
        $this->quizService->checkForEndedQuiz();
    }

    public function returnView()
    {
        $userCategories = $this->user->categoryQuestions()->get()->sortBy('lft');
        $allCategories = CategoryQuestion::withDepth()->get()->sortBy('_lft')->skip(1);
        return view('quiz.chooseCategories.choose', compact('userCategories', 'allCategories'));
    }
}