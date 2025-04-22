<?php
namespace App\Http\Controllers\Desktop;



use App\Services\Quiz\QuizService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Desktop\DesktopService;
use App\Services\Traits\ActorControllerTrait;
use App\Services\Desktop\ControllerTraits\quizListControllerTrait;

class DesktopStudentController extends Controller
{
    private $quizService; 
    private $desktopService;
    private $userId;

    public function __construct()
    {

    }

    
    public function index()
    {
        $userId = Auth::user()->id;
        return view("desktop.student.desktop", compact("userId"));
    }

    public function setting()
    {
        return view("desktop.setting.setting");
    }

}
