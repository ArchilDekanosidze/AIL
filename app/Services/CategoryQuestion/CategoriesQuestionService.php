<?php

namespace App\Services\CategoryQuestion;

use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use Illuminate\Support\Facades\Auth;
use App\Services\CategoryQuestion\Traits\ActorCategoriesQuestionServiceServiceTrait;





class CategoriesQuestionService
{ 
    use ActorCategoriesQuestionServiceServiceTrait;
    public $request;
    private $user;

    const USER_CATEGORY_STATUS_ALL = 'all';
    const USER_CATEGORY_STATUS_NONE = 'none';
    const USER_CATEGORY_STATUS_SOME = 'some';


    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function getDirectcats(CategoryQuestion $category)
    {
        $currentCategory = $category;
        $directCats =  $category->children()->get();
        return $directCats ;
    }

    public function getAncestor(CategoryQuestion $category)
    {
        $currentCategory = $category;
        $ancestor = $currentCategory->ancestors()->get();
        return $ancestor;
    }

    public function getRandomFreeQuestion()
    {
        $currentCategoryId = $this->request->currentCategoryId;
        $categoriesId = $this->getDescendantsAndSelfIds($currentCategoryId);
        $randomQuestion = Question::whereIn('category_question_id', $categoriesId)->test()->inRandomOrder()->first();
        return $randomQuestion;        
    }



    public function addCategoryToUser()
    {
        $currentCategoryId = $this->request->currentCategoryId;
        
        $categoriesId = $this->getDescendantsAndSelfIds($currentCategoryId);
        $parentsId = $this->getParentsIds($currentCategoryId);
        $categoriesId = $categoriesId->merge($parentsId);
        $data = $categoriesId->mapWithKeys(function($id){
            return [$id => ['is_active' => true]];
        })->toArray();
        $this->getUser()->categoryQuestions()->syncWithoutDetaching($data);
        return true;
    }

    public function removeCategoryFromUser()
    {
        $currentCategoryId = $this->request->currentCategoryId;
        $categoriesId = $this->getDescendantsAndSelfIds($currentCategoryId);
       
        $data = $categoriesId->mapWithKeys(function($id){
            return [$id => ['is_active' => false]];
        })->toArray();

        $this->getUser()->categoryQuestions()->syncWithoutDetaching($data);


        return true;
    }

    public function userCategoryStatus($categoryId)
    {
        $allcategoriesId = $this->getDescendantsAndSelfIds($categoryId)->toArray();
        $userSelectedCategoryIds = $this->userSelectedCategoryIds()->toArray();
        $selectedCategoriesId = array_intersect($allcategoriesId, $userSelectedCategoryIds);
        if(empty(array_diff($allcategoriesId, $selectedCategoriesId)))
        {
            $result = self::USER_CATEGORY_STATUS_ALL;
        }
        elseif (empty($selectedCategoriesId)) {
            $result = self::USER_CATEGORY_STATUS_NONE;
        }
        else
        {
            $result = self::USER_CATEGORY_STATUS_SOME;
        }
        return $result;
    }




    public function getDescendantsAndSelfIds($currentCategoryId)
    {
        return CategoryQuestion::descendantsAndSelf($currentCategoryId)->pluck('id');
    }
    public function getParentsIds($currentCategoryId)
    {
        $categoryQuestion = CategoryQuestion::find($currentCategoryId);
        return $categoryQuestion->ancestors()->select('id', 'parent_id')->get()->pluck('id');
    }
    public function userSelectedCategoryIds()
    {
        return $this->getUser()->categoryQuestions()->pluck('category_question_id');
    }
   
}
