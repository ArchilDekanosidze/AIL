<?php
namespace App\Http\Controllers\User\Desktop;



use App\Services\Quiz\QuizService;
use App\Http\Controllers\Controller;
use App\Services\Desktop\DesktopService;

class DesktopStudentController extends Controller
{
    private $quizService;
    private $desktopService;

    public function __construct(QuizService $quizService, DesktopService $desktopService)
    {
        $this->quizService = $quizService;
        $this->desktopService = $desktopService;

    }

    
    public function index()
    {
       return view("desktop.student.desktop");
    }



    public function quizList()
    {
        $this->quizService->checkForEndedQuiz();
        $user = auth()->user();
        $quizzes =  $user->quizzes;
        return view('desktop.student.quizList', compact('quizzes'));
    }

    public function myProgress()
    {

        return view('desktop.student.myProgress');
    }

    public function getChartResult()
    {
        return $this->desktopService->getProgressData();
    }
}
