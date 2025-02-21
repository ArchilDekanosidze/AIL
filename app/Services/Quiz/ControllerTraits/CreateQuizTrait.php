<?php
namespace App\Services\Quiz\ControllerTraits;


use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Services\Quiz\QuizService;
use App\Services\Quiz\ControllerTraits\ActorControllerTrait;



trait CreateQuizTrait 
{

    use ActorControllerTrait;
    
    private $quizService;
    private $selectedQuestions;
    private $quizId;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;

    }

    public function create(Request $request)
    {                    
        if(!$request->has('categorySelected'))
        {
            return Redirect::back()->withErrors(['msg' => 'لطفا حداقل یک دسته بندی انتخاب کنید']);
        }
        
        $this->PreCheck() ;             

        $this->selectedQuestions = $this->quizService->createQuestionsForQuiz();
        $this->creatQuiz();    
        return $this->returnRedirect();
    }

    public function PreCheck()
    {
        $this->quizService->UpdateUserCategorieslevelAndNumber(); 

    }

    public function creatQuiz()
    {
        $quizId = $this->quizService->createQuiz($this->selectedQuestions);
        $this->quizId = $quizId;
    }

    public function returnRedirect()
    {
        return redirect()->route('quiz.online.onlineQuizInProgress', $this->quizId);
    }


}