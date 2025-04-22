<?php
namespace App\Http\Controllers\Quiz;


use App\Models\Quiz;
use Illuminate\Http\Request;
use App\Services\Quiz\QuizService;
use App\Http\Controllers\Controller;

class OnlineQuizController extends Controller
{
 
    private $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;

    }




    public function onlineQuizInProgress(Quiz $quiz)
    {     
        $this->quizService->changeQuizStatusToRuning($quiz);

        $errorMessages = [];  

        $timeLeft = $this->quizService->getTimeleft($quiz);
        if($timeLeft == 0)
        {
            $errorMessages[] = 'زمان این آزمون به اتمام رسیده است';
        }

        $quizQuestion = $this->quizService->selectFirstQuestionForQuiz($quiz);


        $allQuestionAnswered = $this->quizService->allQuestionAnswered;
        if( $allQuestionAnswered == 1)
        {
            $errorMessages[] = 'این آزمون به اتمام رسیده است';
        }       

        $question = $quizQuestion->question;
        // dd($question, $quizQuestion);

        return view('quiz.online.inProgress', compact('quiz', 'question', 'quizQuestion', "errorMessages", "timeLeft", "allQuestionAnswered"));
    }

    public function showAnswer(Request $request)
    {
        $this->quizService->isCurrentQuestionShowedAnswer = 1;
        $answer = $this->quizService->showAnswer();
        return $answer;
    }



    public function nextQuestion()
    {
        $quizQuestion = $this->quizService->nextQuestionOfQuiz();

        if(is_null($quizQuestion))
        {
            $errorMessages = 'خطایی رخ داده است';
            return ['errorMessages' => $errorMessages];
        }

        $question = $quizQuestion->question;
        // dd($question);

        return ["question" => $question, "quizQuestion" => $quizQuestion];
    }

    public function prevQuestion()
    {
        $quizQuestion = $this->quizService->prevQuestionOfQuiz();

        if(is_null($quizQuestion))
        {
            $errorMessages = 'خطایی رخ داده است';
            return ['errorMessages' => $errorMessages];
        }

        $question = $quizQuestion->question;

        return ["question" => $question, "quizQuestion" => $quizQuestion];
    }

    public function saveOnlineQuizDataAndShowResult(Quiz $quiz)
    {
        $this->quizService->saveQuizData($quiz);
        return view('quiz.result.result', compact('quiz'));
 
    }
 


}
