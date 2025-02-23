<?php
namespace App\Http\Controllers\Desktop;



use App\Services\Quiz\QuizService;
use App\Http\Controllers\Controller;
use App\Services\Desktop\DesktopService;
use App\Services\Traits\ActorControllerTrait;
use App\Services\Desktop\ControllerTraits\quizListControllerTrait;

class DesktopStudentController extends Controller
{
    private $quizService;
    private $desktopService;
    private $userId;

    public function __construct(QuizService $quizService, DesktopService $desktopService)
    {
        $this->quizService = $quizService;
        $this->desktopService = $desktopService;
        $this->userId = auth()->user()->id;

    }

    
    public function index()
    {
        $userId = $this->userId;
        return view("desktop.student.desktop", compact("userId"));
    }

    public function setting()
    {
        return view("desktop.setting.setting");
    }







}
