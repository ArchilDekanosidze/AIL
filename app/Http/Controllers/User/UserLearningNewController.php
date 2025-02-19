<?php

namespace App\Http\Controllers\User;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\CategoryQuestion;
use App\Services\Quiz\QuizService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class UserLearningNewController extends Controller
{

    private $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;

    }


    public function start(Request $request)
    {                    
        if(!$request->has('categorySelected'))
        {
            return Redirect::back()->withErrors(['msg' => 'لطفا حداقل یک دسته بندی انتخاب کنید']);
        }
        
        $this->quizService->UpdateUserCategorieslevelAndNumber();    
              

        $selectedQuestions = $this->quizService->createQuestionsForQuiz();
        $quizId = $this->quizService->createQuiz($selectedQuestions);
          
        return redirect()->route('user.learning.onlineQuizInProgress', $quizId);
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
            $errorMessages[] = 'به تمام سوالات این آزمون پاسخ داده اید';
        }       

        $question = $quizQuestion->question;

        return view('user.learning.Quiz.onlineQuizInProgress', compact('quiz', 'question', 'quizQuestion', "errorMessages", "timeLeft", "allQuestionAnswered"));
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

    public function saveQuizDataAndShowResult(Quiz $quiz)
    {
        $this->quizService->saveQuizData($quiz);
        return view('user.learning.Quiz.QuizResult', compact('quiz'));
 
    }


}
