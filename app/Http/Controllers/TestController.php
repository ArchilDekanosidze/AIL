<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\User;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use App\Services\Quiz\QuizService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\Desktop\DesktopService;
use App\Services\Quiz\SubService\SaveQuizDataService;

class TestController extends Controller
{
    private $saveQuizDataService;
    private $desktopService;

    public function __construct(SaveQuizDataService $saveQuizDataService, DesktopService $desktopService)
    {
        $this->saveQuizDataService = $saveQuizDataService;
        $this->desktopService = $desktopService;

    }
    
    public function index()
    {
      // dd(now()->timestamp);
      // $quiz = Quiz::find(110);
      // $this->saveQuizDataService->saveQuizData($quiz);
      $user = Auth::loginUsingId(1);
      $this->desktopService->setUser($user);
      $this->desktopService->getProgressData();

    }
 
}
