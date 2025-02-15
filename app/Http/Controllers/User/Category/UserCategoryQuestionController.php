<?php

namespace App\Http\Controllers\User\Category;

use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\CategoryQuestion\CategoriesQuestionService;


class UserCategoryQuestionController extends Controller
{
    private $categoriesQuestionService;

    public function __construct(CategoriesQuestionService $categoriesQuestionService)
    {
        $this->categoriesQuestionService = $categoriesQuestionService;
    }
    public function index(CategoryQuestion $currentCategory)
    {                   
   
        $directCats = $this->categoriesQuestionService->getDirectcats($currentCategory);   
        $ancestor = $this->categoriesQuestionService->getAncestor($currentCategory);   

        return view('user.category.question.index', compact('currentCategory', 'directCats', 'ancestor'));
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
