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
        Auth::loginUsingId(1, TRUE);        
        $directCats = $this->categoriesQuestionService->getDirectcats($currentCategory);   
        $ancestor = $this->categoriesQuestionService->getAncestor($currentCategory);   

        return view('user.category.question.index', compact('currentCategory', 'directCats', 'ancestor'));
    }

    public function getRandomFreeQuestion(Request $request)
    {       
        $currentCategoryId = $request->currentCategoryId;
        $category = CategoryQuestion::find($currentCategoryId); 
        $randomQuestion = $category->randomFreeQuestion();              
        return $randomQuestion;
    }

    public function add_category_to_user(Request $request)
    {
        $user = auth()->user();
        $currentCategoryId = $request->currentCategoryId;
        $user->add_category_to_user($currentCategoryId);
        return true;
    }

    public function remove_category_from_user(Request $request)
    {
        $user = auth()->user();
        $currentCategoryId = $request->currentCategoryId;
        $user->remove_category_from_user($currentCategoryId);       
        return true;
    }
}
