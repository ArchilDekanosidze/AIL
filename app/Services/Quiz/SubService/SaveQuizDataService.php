<?php
namespace App\Services\Quiz\SubService;

use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use App\Services\Quiz\Trait\QuizTrait;


class SaveQuizDataService
{
    use QuizTrait;
    private $user;
    public $data;


    public function __construct()
    {
        Auth::loginUsingId(1, TRUE);     

        $this->user = auth()->user();
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
        $this->user->categoryQuestions()->syncWithoutDetaching($this->data);
        
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

    public function updatecategoriesQuestion($categoriesQuestion, $isCorrect, $question)
    {
        foreach ($categoriesQuestion as $categoryQuestion)
        {                                    
            $result = $this->newHistory($categoryQuestion, $isCorrect);
                  
            $this->data[$categoryQuestion->id] = ['history' => $result["history"] ,
            'level' =>     $result["level"]          ];            
        }
    }



    public function newHistory($categoryQuestion, $isCorrect)
    {
        $this->setInitialData($categoryQuestion);
        
        $history = $this->data[$categoryQuestion->id]['history'];
        $history[] = ["level" => null, "time" => now(), "isCorrect" => $isCorrect ? 1 : 0];
        $newLevel = $this->newlevel($categoryQuestion, $history);
        $history[count($history) - 1]['level'] = $newLevel;        
        $result["level"] = $newLevel;
        $result["history"] = $history;
        return $result;
    }


    public function newlevel($categoryQuestion, $history)
    {

        $answerHistory =array_map(fn($item) => $item["isCorrect"], $history);
        $newerAnswerHistory = array_slice($answerHistory, -$categoryQuestion->pivot->number_to_change_level);
        $sumAnswerForLevel = 0;
        foreach ($newerAnswerHistory as $answer) {
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

    public function setInitialData($categoryQuestion)
    {
        $isSetData = isset($this->data[$categoryQuestion->id]);
        if($isSetData)
        {
            return;
        }
        $this->data[$categoryQuestion->id]['history'] = json_decode($categoryQuestion->pivot->history , true);
        $this->data[$categoryQuestion->id]['level'] = $categoryQuestion->pivot->level ;
    }
}