<?php
namespace App\Services\Quiz;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\CategoryQuestion;
use Illuminate\Support\Facades\Auth;


class QuizService
{
    private $request;
    private $user;
    private $currentLevels;
    private $targetLevels;
    private $numbers_to_change_level;
    private $categoryPercentage = [];
    private $totalPercentage;
    private $categoriesId;
    private $selectedQuestions ;
    public $quiz;
    public $allQuestionAnswered  = 0;
    public $isCurrentQuestionShowedAnswer = 0;
    public $pivotDataForUpdatingCategory;



    public function __construct(Request $request)
    {
        Auth::loginUsingId(1, TRUE);     

        $this->request = $request;
        $this->user = auth()->user();
        $this->currentLevels = $request->currentLevels;
        $this->targetLevels = $request->targetLevels;
        $this->numbers_to_change_level = $request->numbers_to_change_level;
        $this->categoriesId = $this->request->categorySelected;
        $this->selectedQuestions = collect();

    }

    public function create()
    {
        $this->updateUserCategoriesData();
    }

    public function updateUserCategoriesData()
    {
       
        $this->updateUserCategoriesTargetLevel();
        $this->updateUserCategoriesNumberToChangeLevel();
    }

    public function updateUserCategoriesTargetLevel()
    {
        $data = [];
        foreach ($this->targetLevels  as $categoryId => $targetLevel) {
            $data[$categoryId] = ['target_level' => min($targetLevel, 100)];
        }        
        $this->user->categoryQuestions()->syncWithoutDetaching($data);

    }

    public function updateUserCategoriesNumberToChangeLevel()
    {
        $data = [];
        foreach ($this->numbers_to_change_level  as $categoryId => $number_to_change_level) {
            $data[$categoryId] = ["number_to_change_level" => $number_to_change_level];
        }        
        $this->user->categoryQuestions()->syncWithoutDetaching($data);
    }

    public function createQuestionsForQuiz()
    {
        $this->createCatgoryPercentage();
        $this->selectQuestionsBaseOnPercentage();

        return $this->selectedQuestions;
    }


    public function createCatgoryPercentage()
    {
       $this->fillCatgoriesPercentage();
       $this->setTotalPercentage();
       $this->NormilizeCatgoryPercentage();
    }

    public function fillCatgoriesPercentage()
    {
        foreach ($this->categoriesId as  $categoryId) {
            $category = CategoryQuestion::find($categoryId);
            if($category->descendants()->count() == 0)
            {
                $percentage = $this->targetLevels[$categoryId] - $this->currentLevels[$categoryId];
                $this->categoryPercentage[$categoryId] = $percentage;
            }
        }
    }

    public function setTotalPercentage()
    {
        $this->totalPercentage = 0;
        foreach ($this->categoryPercentage as  $categoryId => $percentage) {
            $this->totalPercentage += $percentage;
        }
        if($this->totalPercentage == 0)
        {
            $this->totalPercentage = 100;
        }
    }

    public function NormilizeCatgoryPercentage()
    {
        foreach ($this->categoryPercentage as  $categoryId => $percentage) {
            $this->categoryPercentage[$categoryId] = $percentage/$this->totalPercentage;            
        }
    }


    public function selectQuestionsBaseOnPercentage()
    {
        //select original question
        $totalQuestions = $this->request->testCount;
        $sumTargetLevel = 0;
        foreach ($this->categoryPercentage as $categoryId => $percentage) {
            $category = CategoryQuestion::find($categoryId);
            if(!$categoryId) continue;
            $numQuestions = $percentage * $totalQuestions;
            $current_level = $this->user->categoryQuestions->find($categoryId)->pivot->level;
            $sumTargetLevel = $sumTargetLevel + $current_level;
            $questions =$category->questions()->orderByRaw('ABS(percentage -?)' , $current_level)
                ->inRandomOrder()->limit($numQuestions)->get()->shuffle();
            $this->selectedQuestions = $this->selectedQuestions->merge($questions);
        }

        //select remaining question 
        $avgTargetlevel = $sumTargetLevel / (count($this->categoryPercentage));
        $questions = Question::whereIn("category_question_id", $this->categoriesId)
            ->whereNotIn("id", $this->selectedQuestions->pluck("id"))
            ->orderByRaw('ABS(percentage -?)' , $avgTargetlevel)
            ->inRandomOrder()->limit($totalQuestions-$this->selectedQuestions->count())->get()->shuffle();

        $this->selectedQuestions = $this->selectedQuestions->merge($questions);
    }

    public function createQuiz()
    {
       $this->createQuizInfo();
       $this->addQuestionsToQuiz();
    }

    public function createQuizInfo()
    {
        $this->quiz = new Quiz();
        if($this->request->quizName != "")
        {
            $this->quiz->quiz_name =  $this->request->quizName;
        }
        else
        {
            $this->quiz->quiz_name =  $this->user->name . "-" . now();
        }
        $this->quiz->quiz_type =  $this->request->action;
        $this->quiz->count = $this->request->testCount;
        $this->quiz->time = $this->request->testTime * 60;
        $this->user->quizzes()->save($this->quiz);
    }

    public function addQuestionsToQuiz()
    {
        $quizQuestionsForInsert = $this->selectedQuestions->map(function($item, $index){
            return[
                'quiz_id' => $this->quiz->id,
                'question_id' => $item->id,
                'place' => $index +1 ,
                'created_at' => now(),
                'updated_at'  => now()
            ];
        });
        QuizQuestion::insert($quizQuestionsForInsert->toArray());
    }

    public function changeQuizStatusToRuning(Quiz $quiz)
    {
        if($quiz->status == "created")
        {
            $quiz->status = "running";
        }
        if($quiz->started_at == null)
        {
            $quiz->started_at = now();
        }
        $quiz->save();
    }

    public function getTimeleft(Quiz $quiz)
    {
        $timePassed = now()->timestamp- Carbon::parse($quiz->started_at)->timestamp;
        $timeLeft=max($quiz->time - $timePassed, 0);
        return $timeLeft;
    }

    public function selectFirstQuestionForQuiz(Quiz $quiz)
    {
        $quizQuestionsNotAnswered = $quiz->quizQuestions->whereNull("user_answer");   
        $quizQuestions = $quiz->quizQuestions;
        if($quizQuestionsNotAnswered->count() == 0)
        {
         $this->allQuestionAnswered = 1;   
         $quizQuestion = $quizQuestions->first();
        }
        else
        {
            $quizQuestion = $quizQuestionsNotAnswered->first();
        }
        return $quizQuestion;

    }

    public function showAnswer()
    {
        $quizId =  $this->request->quizId;
        $quiz = Quiz::find($quizId);
        $questionId =  $this->request->questionId;
        $question = Question::find($questionId);
        $answer = $question->back;

        $this->checkForUpdateQuizData($quiz, $question);
        return $answer;

    }

    public function getUserAnswer()
    {
        $p1CheckBox = $this->request->p1CheckBox;
        $p2CheckBox = $this->request->p2CheckBox;
        $p3CheckBox = $this->request->p3CheckBox;
        $p4CheckBox = $this->request->p4CheckBox;
        $userAnswer = null;
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
        if(is_null($userAnswer) && $this->isCurrentQuestionShowedAnswer)
        {
            $userAnswer = 0;
        }
        return $userAnswer;
    }

    public function checkForUpdateQuizData($quiz, $question)
    {
        $userAnswerStatus =  $quiz->quizQuestions->where("question_id", $question->id)->first()->user_answer;
        if($userAnswerStatus != null)
        {
            return;
        }
        $userAnswer = $this->getUserAnswer();

      
        $timeLeft = $this->getTimeleft($quiz);

        if($timeLeft> 0)
        {
            $this->changeQuizData($userAnswer,  $quiz,  $question);
        }
        else
        {
            $this->saveQuizData($quiz);   
        }
    }
    public function changeQuizData($userAnswer,  $quiz,  $question)
    {
        $quizQuestion = QuizQuestion::where("quiz_id", $quiz->id)->where("question_id", $question->id)->first();
        $quizQuestion->user_answer = $userAnswer;
        $quizQuestion->save();
    }


    public function saveQuizData(Quiz $quiz)
    {
        if($quiz->status == "ended")
        {
            return;
        }
        $rightAnswers = 0;
        $wrongAnswers = 0;
        $notAnswers = 0;
        $quizQuestions = $quiz->quizQuestions;
        foreach ($quizQuestions as $quizQuestion) 
        {
            if($quizQuestion->user_answer == 0 || $quizQuestion->user_answer == null)
            {
                $notAnswers++;
            } 
            else
            {
                $question = Question::find($quizQuestion->question_id);
                if($quizQuestion->user_answer == $question->answer)
                {
                    $rightAnswers++;
                    $this->changeQuestionAndUserCategoryQuestion(1, $question);
                }
                else
                {
                    $wrongAnswers++;     
                    $this->changeQuestionAndUserCategoryQuestion(0, $question);
                }
            }
        }
        $this->user->categoryQuestions()->syncWithoutDetaching($this->pivotDataForUpdatingCategory);
        
        $quiz->rightAnswers = $rightAnswers;
        $quiz->wrongAnswers = $wrongAnswers;
        $quiz->notAnswers = $notAnswers;
        $quiz->finalPercentage =floor(($rightAnswers*3 - $wrongAnswers)*100 /($quiz->count*3));
        $quiz->status = 'ended';

        $quiz->save();
        
    }

    public function changeQuestionAndUserCategoryQuestion($isCorrect, $question)
    {
        $this->changeQuestion($isCorrect, $question);

        $categoriesId = $this->questionAncestorsAndSelfId($question);

        $categoriesQuestion = $this->user->categoryQuestions->whereIn("id", $categoriesId);


        $this->updatecategoriesQuestion($categoriesQuestion, $isCorrect, $question);  

    }

 

    public function changeQuestion($isCorrect, $question)
    {
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
    }

    public function questionAncestorsAndSelfId($question)
    {
        $categoriesId = CategoryQuestion::with('ancestors')->find($question->category_question_id)->ancestors->pluck('id');
        $categoriesId->shift();
        $categoriesId[] = $question->category_question_id;
        return $categoriesId;
    }

    public function updatecategoriesQuestion($categoriesQuestion, $isCorrect, $question)
    {
        foreach ($categoriesQuestion as $categoryQuestion)
        {        
                      
            $newAnswerHistory = $this->newAnswerHistory($categoryQuestion, $isCorrect, $question);
            $newLevel = $this->newlevel($categoryQuestion, $newAnswerHistory);
            $levelHistory = $this->newLevelHistory($categoryQuestion, $newLevel);           
            
            $this->pivotDataForUpdatingCategory[$categoryQuestion->id] = ['answer_history' => $newAnswerHistory ,
            'level_history' => $levelHistory,
            'level' =>     $newLevel          ];            
        }
    }

    public function setInitialPivotData($categoryQuestion)
    {
        $isSetcategoryInPivotData = isset($this->pivotDataForUpdatingCategory[$categoryQuestion->id]);
        if($isSetcategoryInPivotData)
        {
            return;
        }
        $this->pivotDataForUpdatingCategory[$categoryQuestion->id]['answer_history'] = $categoryQuestion->pivot->answer_history ;
        $this->pivotDataForUpdatingCategory[$categoryQuestion->id]['level_history'] = $categoryQuestion->pivot->level_history ;
        $this->pivotDataForUpdatingCategory[$categoryQuestion->id]['level'] = $categoryQuestion->pivot->level ;

    }

    public function newAnswerHistory($categoryQuestion, $isCorrect)
    {

        $this->setInitialPivotData($categoryQuestion);
        $answer_history = $this->pivotDataForUpdatingCategory[$categoryQuestion->id]['answer_history'];
        if(is_null($answer_history))
        {
            $answerHistoryArray[0] = $isCorrect ? 1 : 0;
        }
        else
        {
            $answerHistory = $this->pivotDataForUpdatingCategory[$categoryQuestion->id]['answer_history'];
            $answerHistoryArray =explode(",", $answerHistory);
            $answerHistoryArray[] = $isCorrect ? 1 : 0;
        }
        $answerHistory =implode(",", $answerHistoryArray);
        return $answerHistory;
    }

    public function newlevel($categoryQuestion, $answerHistory)
    {
        $answerHistoryArray =explode(",", $answerHistory);

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
        return $newLevel;
    }

    public function newLevelHistory($categoryQuestion, $newLevel)
    {
        $this->setInitialPivotData($categoryQuestion);

        $levelHistory = $this->pivotDataForUpdatingCategory[$categoryQuestion->id]['level_history'];


        if(is_null($levelHistory))
        {
            $levelHistoryArray[0] = $newLevel;
        }
        else
        {
            $levelHistory = $this->pivotDataForUpdatingCategory[$categoryQuestion->id]['level_history'];

            $levelHistoryArray =explode(",", $levelHistory);
            $levelHistoryArray[] = $newLevel;
        }

        $levelHistory =implode(",", $levelHistoryArray);
        return $levelHistory;
    }

    public function nextQuestionOfQuiz()
    {
        $quizId =  $this->request->quizId;
        $quiz = Quiz::find($quizId);
        $quizQuestionId =  $this->request->quizQuestionId;

        $questionId =  $this->request->questionId;
        $question = Question::find($questionId);


        $this->checkForUpdateQuizData($quiz, $question);
        
        
        $quizQuestion = QuizQuestion::where("quiz_id", $quiz->id)->where("id",">", $quizQuestionId)
        ->orderBy("id")->first();

        return $quizQuestion;
    }

    public function prevQuestionOfQuiz()
    {
        $quizId =  $this->request->quizId;
        $quiz = Quiz::find($quizId);
        $quizQuestionId =  $this->request->quizQuestionId;

        $questionId =  $this->request->questionId;
        $question = Question::find($questionId);

        
        $this->checkForUpdateQuizData($quiz, $question);
            
        $quizQuestion = QuizQuestion::where("quiz_id", $quiz->id)->where("id","<", $quizQuestionId)
        ->orderBy("id", "desc")->first();

        return $quizQuestion;
    }








}