<?php
namespace App\Services\Quiz\Trait;

use App\Models\Quiz;
use App\Models\CategoryQuestion;

trait QuizTrait
{
    public function questionAncestorsAndSelfId($question)
    {
        $categoriesId = CategoryQuestion::with('ancestors')->find($question->category_question_id)->ancestors->pluck('id');
        $categoriesId->shift();
        $categoriesId[] = $question->category_question_id;
        return $categoriesId;
    }

    public function checkForEndedQuiz()
    {
        $quizzes = $this->user->quizzes()->where("status" , "!=" , "ended")->get();
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
}