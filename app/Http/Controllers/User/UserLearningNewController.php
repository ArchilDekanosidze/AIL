<?php

namespace App\Http\Controllers\User;

use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
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
        $quiz->time = $request->testTime * 60;
        $user->quizzes()->save($quiz);
        foreach ($selectedQuestions as $selectedQuestion) {
            $quizQuestion = new QuizQuestion();
            $quizQuestion->quiz_id = $quiz->id;
            $quizQuestion->question_id = $selectedQuestion->id;
            $quizQuestion->save();
        }
        

        return redirect()->route('user.learning.onlineQuizInProgress', $quiz->id);
    }

    public function onlineQuizInProgress(Quiz $quiz)
    {
        $quizQuestions = $quiz->quizQuestions;
        $quizQuestion = $quizQuestions->first();
        $question = $quizQuestion->question;
        
        return view('user.learning.onlineQuizInProgress.onlineQuizInProgress', compact('quiz', 'question', 'quizQuestion'));
    }

    public function showAnswer(Request $request)
    {
        $p1CheckBox = $request->p1CheckBox;
        $p2CheckBox = $request->p2CheckBox;
        $p3CheckBox = $request->p3CheckBox;
        $p4CheckBox = $request->p4CheckBox;
        $userAnswer = 0;
        if($p1CheckBox == "true")
        {
            $userAnswer = 1;
        }
        if($p2CheckBox == "true")
        {
            $userAnswer = 2;
        }
        if($p3CheckBox == "true")
        {
            $userAnswer = 3;
        }
        if($p4CheckBox == "true")
        {
            $userAnswer = 4;
        }
        $quizId =  $request->quizId;
        $quiz = Quiz::find($quizId);
        $questionId =  $request->questionId;
        $question = Question::find($questionId);
        $this->changeQuizData($userAnswer,  $quiz,  $question);
        $this->changeQuestionAndUserCategoryQuestion($userAnswer, $question->id);
        $answer = $question->back;

      
        return $answer;
    }

    public function changeQuizData($userAnswer,  $quiz,  $question)
    {
        $quizQuestion = QuizQuestion::where("quiz_id", $quiz->id)->where("question_id", $question->id)->first();
        $quizQuestion->user_answer = $userAnswer;
        $quizQuestion->save();
    }

    public function changeQuestionAndUserCategoryQuestion($userAnswer, $questionId)
    {
        $question = Question::find($questionId);
        $isCorrect = $userAnswer == $question->answer;
        if($isCorrect)
        {
            $question->percentage =min(($question->percentage * $question->count + 3)/$question->count , 100);
        }
        else
        {
            $question->percentage =max( ($question->percentage * $question->count -1)/($question->count), 0 );
        }
        $question->count = $question->count +1;
        $question->save();
        $user = auth()->user();
        $categoryQuestion = $user->categoryQuestions->where("id", $question->category_question_id)->first();

        $answerHistory = $categoryQuestion->pivot->answer_history;
        if($answerHistory == "")
        {
            $answerHistoryArray[0] = $isCorrect ? 1 : 0;
        }
        else
        {
            $answerHistoryArray =explode(",", $answerHistory);
            $answerHistoryArray[] = $isCorrect ? 1 : 0;
        }
        $answerHistory =implode(",", $answerHistoryArray);
        $newerAnswerHistoryArray = array_slice($answerHistoryArray, -$categoryQuestion->pivot->number_to_change_level);
        $sumAnswerForLevel = 0;
        foreach ($newerAnswerHistoryArray as $answer) {
            if($answer == 1)
            {
                $sumAnswerForLevel = $sumAnswerForLevel + 3;
            }
            else if($answer == 0)
            {
                $sumAnswerForLevel = $sumAnswerForLevel -1;
            }
        }
        $newLevel =(int) ($sumAnswerForLevel / ($categoryQuestion->pivot->number_to_change_level*3) * 100);
        $levelHistory = $categoryQuestion->pivot->level_history;

        if($levelHistory == "")
        {
            $levelHistoryArray[0] = $newLevel;
        }
        else
        {
            $levelHistoryArray =explode(",", $levelHistory);
            $levelHistoryArray[] = $newLevel;
        }

        $levelHistory =implode(",", $levelHistoryArray);


        $data = [];
            $data[$question->category_question_id] = ['answer_history' => $answerHistory ,
                 'level_history' => $levelHistory,
                 'level' =>     $newLevel          ];
        $user->categoryQuestions()->syncWithoutDetaching($data);


        dd($answerHistory);
        dd($categoryQuestion->pivot->level_history);
    }


}
