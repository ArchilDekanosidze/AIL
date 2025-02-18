<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use App\Services\Quiz\QuizService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\Desktop\DesktopService;

class TestController extends Controller
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
      $this->desktopService->getProgressData();
    }
 
}
