<?php
namespace App\Http\Controllers\FreeQuestion;



use App\Models\User;
use Illuminate\Support\Str;
use App\Models\FreeQuestion;
use Illuminate\Http\Request;
use App\Services\Quiz\QuizService;
use App\Models\FreeQuestionComment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Desktop\DesktopService;
use App\Services\Traits\ActorControllerTrait;
use App\Services\FreeQuestion\FreeQuestionService;
use App\Services\Desktop\ControllerTraits\quizListControllerTrait;

class FreeQuestionCommentNewController extends Controller
{    


    private $freeQuestionService;

    public function __construct(FreeQuestionService $freeQuestionService)
    {
        $this->freeQuestionService = $freeQuestionService;
    }


    public function newComment()
    {
        return $this->freeQuestionService->newComment();
    }





}
