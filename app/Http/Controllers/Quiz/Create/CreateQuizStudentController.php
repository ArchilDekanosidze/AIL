<?php
namespace App\Http\Controllers\Quiz\Create;


use Illuminate\Http\Request;
use App\Services\Quiz\QuizService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;


class CreateQuizStudentController extends Controller
{
    
    private $quizService;

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
        
        $this->quizService->UpdateUserCategorieslevelAndNumber(); 
              

        $selectedQuestions = $this->quizService->createQuestionsForQuiz();
        $quizId = $this->quizService->createQuiz($selectedQuestions);
        dd($quizId);
          
        return redirect()->route('user.learning.onlineQuizInProgress', $quizId);
    }



}
