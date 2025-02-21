<?php
namespace App\Services\Quiz\SubService;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Quiz\Traits\ActorTrait;

class CreateQuizService
{
    use ActorTrait;
    public $quiz;
    private $quizName;
    private $quiz_type;
    private $testCount;
    private $testTime;
    private $selectedQuestions;

    public function __construct(Request $request)
    {
        $this->quizName = $request->quizName;
        $this->quiz_type = $request->action;
        $this->testCount = $request->testCount;
        $this->testTime = $request->testTime;

    }

    public function createQuiz($selectedQuestions)
    {
        $this->selectedQuestions = $selectedQuestions;
        $this->createQuizInfo();
        $this->addQuestionsToQuiz();
        return $this->quiz->id;
    }

    public function createQuizInfo()
    {
        $this->quiz = new Quiz();
        if($this->quizName != "")
        {
            $this->quiz->quiz_name =  $this->quizName;
        }
        else
        {
            $this->quiz->quiz_name =  $this->user->name . "-" . now();
        }
        $this->quiz->quiz_type =  $this->quiz_type;
        $this->quiz->count = $this->testCount;
        $this->quiz->time = $this->testTime * 60;
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
}