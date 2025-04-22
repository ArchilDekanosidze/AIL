<?php
namespace App\Http\Controllers\FreeQuestion;



use App\Models\User;
use App\Models\FreeTag;
use App\Models\FreeQuestion;
use Illuminate\Http\Request;
use App\Services\Quiz\QuizService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Desktop\DesktopService;
use App\Services\Traits\ActorControllerTrait;
use App\Services\FreeQuestion\FreeQuestionService;
use App\Services\Desktop\ControllerTraits\quizListControllerTrait;

class FreeQuestionController extends Controller
{    
    private $freeQuestionService;

    public function __construct(FreeQuestionService $freeQuestionService)
    {
        $this->freeQuestionService = $freeQuestionService;
    }

    public function index()
    {      
        $freeTags = FreeTag::all(); 
        // dd($freeTags);
        return view("freeQuestion.index", compact('freeTags'));
    }


    public function fetchFreeQuestions()
    {
       return $this->freeQuestionService->fetchFreeQuestions();
    }


}
