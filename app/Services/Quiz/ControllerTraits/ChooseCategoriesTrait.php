<?php
namespace App\Services\Quiz\ControllerTraits;

use App\Models\CategoryQuestion;
use App\Services\Quiz\QuizService;
use App\Services\Traits\ActorControllerTrait;



trait ChooseCategoriesTrait 
{

    use ActorControllerTrait;
    
    private $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;

    }

    public function chooseCategories()
    {       
        $this->PreCheck();
        $this->setUser();
        $user = $this->getUser();
        return $this->returnRedirect();
    }
    




    public function PreCheck()
    {
        $this->quizService->checkForEndedQuiz();
    }

    public function returnRedirect()
    {
        $userCategories = $this->quizService->getUser()->categoryQuestions()->get()->sortBy('lft');
        $allCategories = CategoryQuestion::withDepth()->get()->sortBy('_lft')->skip(1);
        return view('quiz.chooseCategories.choose', compact('userCategories', 'allCategories'));
    }
}