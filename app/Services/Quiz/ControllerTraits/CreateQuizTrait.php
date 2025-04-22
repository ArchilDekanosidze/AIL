<?php
namespace App\Services\Quiz\ControllerTraits;


use Illuminate\Http\Request;
use App\Services\Quiz\QuizService;
use Illuminate\Support\Facades\Redirect;
use App\Services\Traits\ActorControllerTrait;



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
        // dd($request->all());
        // dd()
        if(!$request->has('categorySelected'))
        {
            return Redirect::back()->withErrors(['msg' => 'لطفا حداقل یک دسته بندی انتخاب کنید']);
        }
        if($request->testCount == 0)
        {
            return Redirect::back()->withErrors(['msg' => 'تعداد سوالات انتخابی نمی تواند کمتر از یک باشد']);
        }
        
        $this->PreCheck() ;             

        $this->selectedQuestions = $this->quizService->createQuestionsForQuiz();
        if(count($this->selectedQuestions) == 0)
        {
            return Redirect::back()->withErrors(['msg' => 'دسته بندی های انتخابی فاقد سوال می باشند. لطفا دسته بندی جدیدی انتخابی کنید']);
        }
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