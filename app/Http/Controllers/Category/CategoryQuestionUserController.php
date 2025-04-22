<?php
namespace App\Http\Controllers\Category;


use App\Models\Tag;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Traits\ActorControllerTrait;
use App\Services\CategoryQuestion\CategoriesQuestionService;


class CategoryQuestionUserController extends Controller
{


   

    private $categoriesQuestionService;
    use ActorControllerTrait;


    public function __construct(CategoriesQuestionService $categoriesQuestionService)
    {
        $this->categoriesQuestionService = $categoriesQuestionService;
    }
    public function index(CategoryQuestion $currentCategory, Request $request)
    {                   
        $directCats = $this->categoriesQuestionService->getDirectcats($currentCategory);   
        $ancestor = $this->categoriesQuestionService->getAncestor($currentCategory);   

        // $question = Question::find(123);
        // dd($question->front);

        return view('category.categoryQuestion.user.index', compact('currentCategory', 'directCats', 'ancestor'));
    }

    public function getRandomFreeQuestion()
    {       
        return $this->categoriesQuestionService->getRandomFreeQuestion();
    }

    public function addCategoryToUser()
    {
        $this->categoriesQuestionService->addCategoryToUser();
        return true;
    }

    public function removeCategoryFromUser()
    {
        $this->categoriesQuestionService->removeCategoryFromUser();
        return true;
    }


    
}
