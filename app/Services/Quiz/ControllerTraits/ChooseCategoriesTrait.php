<?php
namespace App\Services\Quiz\ControllerTraits;

use App\Models\CategoryQuestion;
use App\Services\Quiz\QuizService;
use App\Services\Traits\ActorControllerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait ChooseCategoriesTrait 
{

    use ActorControllerTrait;
    
    private $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;

    }

    public function chooseCategories(Request $request)
    {       
        $this->PreCheck();
        return $this->returnRedirect();
    }
    




    public function PreCheck()
    {
        $this->quizService->checkForEndedQuiz();
    }

    public function returnRedirect()
    {
        $userCategories = $this->quizService->getUser()->categoryQuestions()->get()->sortBy('lft');
        $userCategoriesHashSet = array_flip($userCategories->pluck('id')->toArray());
        $allCategories = CategoryQuestion::withDepth()->get()->sortBy('_lft')->skip(1);
        // dd(2);
        $categoryIdsWithSubcategories = CategoryQuestion::whereHas('descendants')->pluck('id')->toArray();
        $categoryIdsWithSubcategories = array_flip($categoryIdsWithSubcategories);
        return view('quiz.chooseCategories.choose', compact('userCategories', 'allCategories', 'categoryIdsWithSubcategories', 'userCategoriesHashSet'));
    }


}