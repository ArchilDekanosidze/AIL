<?php

namespace App\Http\Controllers\User;

use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use App\Http\Controllers\Controller;
use App\Models\Quiz;
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
        $currentLevels = $request->currentLevels;
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
        $categoryPercentage = [];
        $totalPercentage = 0;
        foreach ($categoriesId as  $categoryId) {
            $category = CategoryQuestion::find($categoryId);
            if($category->descendants()->count() == 0)
            {
                $percentage = $targetLevels[$categoryId] - $currentLevels[$categoryId];
                $categoryPercentage[$categoryId] = $percentage;
                $totalPercentage += $percentage;
            }
        }
        $totalQuestions = $request->testCount;
        $selectedQuestions = collect();
        foreach ($categoryPercentage as $categoryId => $percentage) {
            $category = CategoryQuestion::find($categoryId);
            if(!$categoryId) continue;
            $numQuestions = floor($percentage/$totalPercentage) * $totalQuestions;
            $questions =$category->questions()
            ->inRandomOrder()->limit($numQuestions)->get()->shuffle();
            $selectedQuestions = $selectedQuestions->merge($questions);
        }


        $questions = Question::whereIn("category_question_id", $categoriesId)
            ->inRandomOrder()->limit($totalQuestions-$selectedQuestions->count())->get()->shuffle();

        $selectedQuestions = $selectedQuestions->merge($questions);

            


        $quiz = new Quiz();
        if($request->quizName != "")
        {
            $quiz->quiz_name =  $request->quizName;
        }
        else
        {
            $quiz->quiz_name =  $user->name . "-" . now();
        }
        $quiz->quiz_type =  $request->action;
        $quiz->count = $request->testCount;
        $quiz->time = $request->testTime;
        $selectedQuestions = $selectedQuestions->map(function ($question){
            $question["user_answer"] = "0";
            $question["answer_status"] = "0";
            return $question;
        });

        $quiz->data = $selectedQuestions;
        
        $user->quizzes()->save($quiz);
        
        return redirect()->route('user.learning.onlineQuizInProgress');
    }

    public function onlineQuizInProgress()
    {
        return view('user.learning.onlineQuizInProgress.onlineQuizInProgress');
    }


}
