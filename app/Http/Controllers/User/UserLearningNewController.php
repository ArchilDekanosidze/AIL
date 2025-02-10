<?php

namespace App\Http\Controllers\User;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        $currentLevels = $request->currentLevels;
        $targetLevels = $request->targetLevels;
        $numbers_to_change_level = $request->numbers_to_change_level;
        $data = [];
        foreach ($targetLevels  as $categoryId => $targetLevel) {
            $data[$categoryId] = ['target_level' => min($targetLevel, 100)];
        }        
        $user->categoryQuestions()->syncWithoutDetaching($data);

        foreach ($numbers_to_change_level  as $categoryId => $number_to_change_level) {
            $data[$categoryId] = ["number_to_change_level" => $number_to_change_level];
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
        $sumTargetLevel = 0;
        foreach ($categoryPercentage as $categoryId => $percentage) {
            $category = CategoryQuestion::find($categoryId);
            if(!$categoryId) continue;
            $numQuestions = floor($percentage/$totalPercentage) * $totalQuestions;
            $current_level = $user->categoryQuestions->find($categoryId)->pivot->level;
            $sumTargetLevel = $sumTargetLevel + $current_level;
            $questions =$category->questions()->orderByRaw('ABS(percentage -?)' , $current_level)
                ->inRandomOrder()->limit($numQuestions)->get()->shuffle();
            $selectedQuestions = $selectedQuestions->merge($questions);
        }
        $avgTargetlevel = $sumTargetLevel / (count($categoryPercentage));
        $questions = Question::whereIn("category_question_id", $categoriesId)
            ->whereNotIn("id", $selectedQuestions->pluck("id"))
            ->orderByRaw('ABS(percentage -?)' , $avgTargetlevel)
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
        // foreach ($selectedQuestions as $selectedQuestion) {
        //     $quizQuestion = new QuizQuestion();
        //     $quizQuestion->quiz_id = $quiz->id;
        //     $quizQuestion->question_id = $selectedQuestion->id;
        //     $quizQuestion->save();
        // }
        $quizQuestionsForInsert = $selectedQuestions->map(function($item) use($quiz){
            return[
                'quiz_id' => $quiz->id,
                'question_id' => $item->id,
                'created_at' => now(),
                'updated_at'  => now()
            ];
        });
        QuizQuestion::insert($quizQuestionsForInsert->toArray());
        

        return redirect()->route('user.learning.onlineQuizInProgress', $quiz->id);
    }

    public function onlineQuizInProgress(Quiz $quiz)
    {     
        $errorMessages = [];  
        if($quiz->status == "created")
        {
            $quiz->status = "running";
        }
        if($quiz->started_at == null)
        {
            $quiz->started_at = now();
        }
        $quiz->save();

        $timePassed = now()->timestamp- Carbon::parse($quiz->started_at)->timestamp;
        $timeLeft=max($quiz->time - $timePassed, 0);
       if($timeLeft == 0)
       {
        $errorMessages[] = 'زمان این آزمون به اتمام رسیده است';
       }
        $quizQuestions = $quiz->quizQuestions;
        $quizQuestion = $quizQuestions->first();
        $question = $quizQuestion->question;

        return view('user.learning.onlineQuizInProgress.onlineQuizInProgress', compact('quiz', 'question', 'quizQuestion', "errorMessages", "timeLeft"));
    }

    public function showAnswer(Request $request)
    {

        $quizId =  $request->quizId;
        $quiz = Quiz::find($quizId);
        $questionId =  $request->questionId;
        $question = Question::find($questionId);
        $userAnswerStatus =  $quiz->quizQuestions->where("question_id", $questionId)->first();
        $answer = $question->back;

        if($userAnswerStatus != null)
        {
            return $answer;
        }

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
     


        $timePassed = now()->timestamp- Carbon::parse($quiz->started_at)->timestamp;
        $timeLeft=max($quiz->time - $timePassed, 0);
        if($timeLeft> 0)
        {
            $this->changeQuizData($userAnswer,  $quiz,  $question);
            $this->changeQuestionAndUserCategoryQuestion($userAnswer, $question);
        }
      
        return $answer;
    }

    public function changeQuizData($userAnswer,  $quiz,  $question)
    {
        $quizQuestion = QuizQuestion::where("quiz_id", $quiz->id)->where("question_id", $question->id)->first();
        $quizQuestion->user_answer = $userAnswer;
        $quizQuestion->save();
    }

    public function changeQuestionAndUserCategoryQuestion($userAnswer, $question)
    {
        // $question = Question::find($questionId);
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
        $categoriesId = CategoryQuestion::with('ancestors')->find($question->category_question_id)->ancestors->pluck('id');
        $categoriesId->shift();
        $categoriesId[] = $question->category_question_id;
        $categoriesQuestion = $user->categoryQuestions->whereIn("id", $categoriesId);


        $data = [];
        foreach ($categoriesQuestion as $categoryQuestion)
         {        
            
            // dd($categoryQuestion);
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
            $newLevel = min(100, $newLevel);
            $newLevel = max(0, $newLevel);

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


            $data[$categoryQuestion->id] = ['answer_history' => $answerHistory ,
                'level_history' => $levelHistory,
                'level' =>     $newLevel          ];

            }

        $user->categoryQuestions()->syncWithoutDetaching($data);
    }


}
