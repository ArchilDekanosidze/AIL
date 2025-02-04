<?php

namespace App\Http\Controllers\User;

use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class UserLearningNewController extends Controller
{

    public function chooseCategory()
    {       
        $user = auth()->user();
        $userCategories = $user->categoryQuestions()->get()->sortBy('lft');
        // dd($userCategories->first()->pivot->level);
        $allCategories = CategoryQuestion::withDepth()->get()->sortBy('_lft')->skip(1);
        // dd($userCategories);
        // dd($allCategories);
        // dd($mainCategory->children()->get());
        // dd($allCategories->sortBy('_lft')->pluck('name'));
       return view('user.learning.new.chooseCategory', compact('userCategories', 'allCategories'));
    }

    public function start(Request $request)
    {
        $user = auth()->user();
        $targetLevels = $request->targetLevels;
        $numbers_to_change_level = $request->numbers_to_change_level;
        $data = [];
        foreach ($targetLevels  as $categoryId => $targetLevel) {
            $data[$categoryId] = ['target_level' => min($targetLevel, 100) 
                                , "number_to_change_level" => $numbers_to_change_level[$categoryId]];
        }        
        $user->categoryQuestions()->syncWithoutDetaching($data);
       
        if(!$request->has('categorySelected'))
        {
            return Redirect::back()->withErrors(['msg' => 'لطفا حداقل یک دسته بندی انتخاب کنید']);
        }
     
        $categoriesId = $request->categorySelected;
        $questions = Question::whereIn("category_question_id", $categoriesId)
            ->inRandomOrder()->limit($request->testCount)->get()->shuffle();
        dd($questions);


        dd($request->all());
    }
}
