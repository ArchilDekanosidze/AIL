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

    public function clearCache()
    {
        CategoryQuestion::clearCache();
        dd('cache is Cleared');
    }

    public function returnRedirect()
    {        
        $userCategories = $this->quizService->getUser()->categoryQuestions()->get()->sortBy('lft');
        $userCategoriesHashSet = array_flip($userCategories->pluck('id')->toArray());
        $allCategories = CategoryQuestion::getCachedAllCategories();
        $categoryIdsWithSubcategories = CategoryQuestion::getCachedAllCategoryIdsWithSubcategories();
        $categoryIdsWithSubcategories = array_flip($categoryIdsWithSubcategories);
        return view('quiz.chooseCategories.choose', compact('userCategories', 'allCategories', 'categoryIdsWithSubcategories', 'userCategoriesHashSet'));
    }


}