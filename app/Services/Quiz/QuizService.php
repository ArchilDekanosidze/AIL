<?php
namespace App\Services\Quiz;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Quiz\Traits\QuizTrait;
use App\Services\Quiz\Traits\OnlineQuizTrait;
use App\Services\Quiz\SubService\CreateQuizService;
use App\Services\Quiz\Traits\ActorQuizServiceTrait;
use App\Services\Quiz\SubService\SaveQuizDataService;
use App\Services\Quiz\SubService\CreateQuizQuestionService;
use App\Services\Quiz\SubService\UpdateUserCategorieslevelAndNumberService;

class QuizService
{   use QuizTrait, OnlineQuizTrait, ActorQuizServiceTrait;
    public $quiz;
    public $request;
    public $allQuestionAnswered  = 0;
    public $isCurrentQuestionShowedAnswer = 0;
    public $pivotDataForUpdatingCategory;
    public $saveQuizDataService;
    public $updateUserCategorieslevelAndNumberService;
    private $createQuizQuestionService;
    private $createQuizService;



    public function __construct(Request $request,
    SaveQuizDataService $saveQuizDataService, 
     UpdateUserCategorieslevelAndNumberService $updateUserCategorieslevelAndNumberService,
     CreateQuizQuestionService $createQuizQuestionService,
     CreateQuizService $createQuizService)
    {
        $this->request = $request;
        $this->saveQuizDataService = $saveQuizDataService;
        $this->updateUserCategorieslevelAndNumberService = $updateUserCategorieslevelAndNumberService;
        $this->createQuizQuestionService = $createQuizQuestionService;
        $this->createQuizService = $createQuizService;

        // Auth::loginUsingId(1);
        // $this->setUser(auth()->user());

    }

    public function UpdateUserCategorieslevelAndNumber()
    {
        $this->updateUserCategorieslevelAndNumberService->updateUserCategoriesData();
    }

    public function createQuestionsForQuiz()
    {
        return $this->createQuizQuestionService->createQuestionsForQuiz();
    }

    public function createQuiz($selectedQuestions)
    {
        return $this->createQuizService->createQuiz($selectedQuestions);
    }
}