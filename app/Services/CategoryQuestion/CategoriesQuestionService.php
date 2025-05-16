<?php

namespace App\Services\CategoryQuestion;

use App\Models\User;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use Illuminate\Support\Facades\DB;
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
        for ($i=0; $i < 10; $i++) { 
            
            $currentCategoryId = $this->request->currentCategoryId;
            $categoriesId = $this->getDescendantsAndSelfIds($currentCategoryId);
            $categoryId = $categoriesId[rand(0,$categoriesId->count()-1)];
            $randomQuestion = Question::where('category_question_id', $categoryId)->where('isfree', 1)->test()->orderByRaw('rand()')->first();
            if($randomQuestion)
            {
                return $randomQuestion;         
            }
        }
    }



    public function addCategoryToUser()
    {
        $currentCategoryId = $this->request->currentCategoryId;        
        $categoriesId = $this->getDescendantsAndSelfIds($currentCategoryId);

        $parentsId = $this->getParentsIds($currentCategoryId);
        $categoriesId = $categoriesId->merge($parentsId);
        
        $allCategories = CategoryQuestion::whereIn('id', $categoriesId)->get();
        
        $allUserCategoryQuestion = DB::table('user_category_question')
        ->where('user_id', $this->getUser()->id)
        ->whereIn('category_question_id', $categoriesId)
        ->get();    

        $data = $categoriesId->map(function($id) use($allCategories, $allUserCategoryQuestion){
            $cat = $allCategories->where('id', $id)->first();
            $firstUserCategoryQuestion = $allUserCategoryQuestion->where('category_question_id', $id)->first();
            if(is_null($firstUserCategoryQuestion))
            {
                // $number_to_change_level = max(50, $cat->question_count);
                 $number_to_change_level = 10;
            }
            else
            {
                $number_to_change_level = $firstUserCategoryQuestion->number_to_change_level;
            }

            // dd($cat);
            return ['user_id' => $this->getUser()->id,
                    'category_question_id' => $id,
                    'is_active' => true,
                    'number_to_change_level' => $number_to_change_level                                
            ];
        })->toArray();

        // dd($data);

        DB::table('user_category_question')->upsert($data, ['user_id', 'category_question_id']);
        
        return true;
    }

    public function removeCategoryFromUser()
    {
        $currentCategoryId = $this->request->currentCategoryId;
        $categoriesId = $this->getDescendantsAndSelfIds($currentCategoryId);
       
        //

        $data = $categoriesId->map(function($id){
            return ['user_id' => $this->getUser()->id, 'category_question_id' => $id, 'is_active' => false];
        })->toArray();

        DB::table('user_category_question')->upsert($data, ['user_id', 'category_question_id']);

        // $this->getUser()
        //     ->categoryQuestions()
        //     ->newPivotStatement()
        //     ->whereIn('category_question_id', $categoriesId)
        //     ->update(['is_active' => false ]);




        // $data = $categoriesId->mapWithKeys(function($id){
        //     return [$id => ['is_active' => false]];
        // })->toArray();

        // $this->getUser()->categoryQuestions()->syncWithoutDetaching($data);

        

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
