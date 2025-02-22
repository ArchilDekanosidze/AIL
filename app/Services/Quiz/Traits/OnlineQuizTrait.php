<?php
namespace App\Services\Quiz\Traits;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizQuestion;
use Illuminate\Support\Carbon;


trait OnlineQuizTrait
{
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
        if($quizQuestionsNotAnswered->count() == 0 || $quiz->status == "ended")
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