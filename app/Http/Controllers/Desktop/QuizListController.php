<?php
namespace App\Http\Controllers\Desktop;



use App\Models\User;
use App\Services\Quiz\QuizService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile\UserRelationship;

class QuizListController extends Controller
{
    private $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;

    }


    public function quizList(User $user)
    {
       
        $this->preCheckQuizList();                        
        $authUser = auth()->user();
        $isSupervisor = UserRelationship::where('supervisor_id', $authUser->id)
            ->where('student_id', $user->id)
            ->exists();

        if ($authUser->id !== $user->id && !$isSupervisor) {
            abort(403, 'شما مجاز به مشاهده این آزمون‌ها نیستید.');
        }  
       
                      
        $quizzes =  $user->quizzes;
        $quizzes = $quizzes->sortByDesc("started_at");

        return view('desktop.quizList.index', compact('quizzes', 'isSupervisor'));
    }

    public function preCheckQuizList()
    {
        $this->quizService->checkForEndedQuiz();
    }

}
