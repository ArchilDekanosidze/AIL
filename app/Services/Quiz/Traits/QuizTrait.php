<?php
namespace App\Services\Quiz\Traits;

use App\Models\Quiz;
use App\Models\CategoryQuestion;
use App\Services\Traits\HistoryFileTrait;

trait QuizTrait
{
    use HistoryFileTrait;
    public function questionAncestorsAndSelfId($question)
    {
        $categoriesId = CategoryQuestion::with('ancestors')->find($question->category_question_id)->ancestors->pluck('id');
        $categoriesId->shift();
        $categoriesId[] = $question->category_question_id;
        return $categoriesId;
    }

    public function checkForEndedQuiz()
    {
        
        $quizzes = $this->getUser()->quizzes()->where("status" , "!=" , "ended")->get();
        foreach ($quizzes as $quiz) {
            $timeLeft = $this->getTimeleft($quiz);
            if($timeLeft == 0)
            {
                $this->saveQuizData($quiz);  
            }
        }
    }

    public function saveQuizData(Quiz $quiz)
    {
        $this->saveQuizDataService->saveQuizData($quiz);  
    }

    public function decayPercentage()
    {
        $categoryQuestions = $this->getUser()
            ->categoryQuestions()
            ->wherePivot('is_active', 1)
            ->wherePivot('decay_at', '<=', now()->subDay())
            ->get();
        $data =[];
        foreach ($categoryQuestions as $categoryQuestion) {            
            $bridgeId = $categoryQuestion->pivot->id;
            $history = [];
            if($this->getHistory($bridgeId) != null)
            {
                $oldHistory = $this->getHistory($bridgeId);
                foreach ($oldHistory as $old) {
                    $history[] = $old;
                }
            }            
            $decay = $categoryQuestion->pivot->decay;
            try 
            {
                $lastIndex = count($history) - 1;
                $daysPassed = now()->diffInDays($categoryQuestion->pivot->decay_at);
                $levelDecay = $daysPassed*$decay;
                $newLevel = $history[$lastIndex]['level'] - $levelDecay;
                $newLevel = max( $newLevel,   1);
                $history[$lastIndex]['level'] = $newLevel;
                $this->saveHistory($bridgeId, $history);
                $data[$categoryQuestion->id] = ['level' => $newLevel, 'decay_at' => now()];
            } catch (\Throwable $th) {
                dump($categoryQuestion);
            }

        }
        dd($data);

        $this->getUser()->categoryQuestions()->syncWithoutDetaching($data);
    }
}