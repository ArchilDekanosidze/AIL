<?php
namespace App\Http\Controllers\FreeQuestion;



use App\Models\User;
use App\Models\FreeTag;
use Illuminate\Support\Str;
use App\Models\FreeQuestion;
use Illuminate\Http\Request;
use App\Services\Quiz\QuizService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Desktop\DesktopService;
use App\Services\Traits\ActorControllerTrait;
use App\Services\FreeQuestion\FreeQuestionService;
use App\Services\Desktop\ControllerTraits\quizListControllerTrait;

class FreeQuestionEditController extends Controller
{    

    private $freeQuestionService;

    public function __construct(FreeQuestionService $freeQuestionService)
    {
        $this->freeQuestionService = $freeQuestionService;
    }
    
    public function update()
    {
       return $this->freeQuestionService->updateQuestion();
    }

    public function destroy(FreeQuestion $question)
    {
        abort_if(auth()->id() !== 1, 403);
        $question->delete();

        return response()->json(['success' => true]);
    }

    




    

}
