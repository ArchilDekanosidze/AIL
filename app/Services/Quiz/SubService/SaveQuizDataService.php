<?php
namespace App\Services\Quiz\SubService;

use App\Models\Quiz;
use App\Models\Question;
use App\Services\Traits\ActorTrait;
use Illuminate\Support\Facades\Auth;
use App\Services\Quiz\Traits\QuizTrait;
use App\Services\Traits\HistoryFileTrait;


class SaveQuizDataService
{
    use QuizTrait, ActorTrait, HistoryFileTrait;
    public $data;


    public function __construct()
    {
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
        $this->getUser()->categoryQuestions()->syncWithoutDetaching($this->data);
        
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
        $categoriesQuestion = $this->getUser()->categoryQuestions->whereIn("id", $categoriesId);


        $this->updatecategoriesQuestion($categoriesQuestion, $isCorrect, $question);  


    }

    public function changeQuestion($isCorrect, $question)
    {
        if($isCorrect) 
        {
            $question->percentage =max(($question->percentage * $question->count -1)/$question->count , 1);
        }
        else
        {
            $question->percentage =min( ($question->percentage * $question->count +1)/($question->count), 100 );
        }
        $question->count = $question->count +1;
        $question->save();
    }

    public function updatecategoriesQuestion($categoriesQuestion, $isCorrect, $question)
    {
        foreach ($categoriesQuestion as $categoryQuestion)
        {                                    
            $newLevel = $this->newHistory($categoryQuestion, $isCorrect);
            $bridgeId = $categoryQuestion->pivot->id;

            $this->data[$categoryQuestion->id] = ['level' =>  $newLevel];            
        }
    }



    public function newHistory($categoryQuestion, $isCorrect)
    {
        $this->setInitialData($categoryQuestion);

        $bridgeId = $categoryQuestion->pivot->id;
        if($this->getHistory($bridgeId) != null)
        {
            $oldHistory = $this->getHistory($bridgeId);
            foreach ($oldHistory as $old) {
                $history[] = $old;
            }
        }
        
        $history[] = ["level" => null, "time" => now()->timestamp, "isCorrect" => $isCorrect ? 1 : 0];
        $newLevel = $this->newlevel($categoryQuestion, $history);
        $history[count($history) - 1]['level'] = $newLevel;        
        // $result["level"] = $newLevel;
        // $result["history"] = $history;
        $this->saveHistory($bridgeId, $history);

        return $newLevel;
    }


    public function newlevel($categoryQuestion, $history)
    {
        try {
            $answerHistory =array_map(fn($item) => $item['isCorrect'], $history);
            //code...
        } catch (\Throwable $th) {
            dd($history);
            //throw $th;
        }
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
        $newLevel = max(1, $newLevel);
        return $newLevel;
    }

    public function setInitialData($categoryQuestion)
    {
        $isSetData = isset($this->data[$categoryQuestion->id]);
        if($isSetData)
        {
            return;
        }
        $this->data[$categoryQuestion->id]['level'] = $categoryQuestion->pivot->level ;
    }
}