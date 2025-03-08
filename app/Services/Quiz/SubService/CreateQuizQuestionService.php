<?php
namespace App\Services\Quiz\SubService;

use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use App\Services\Traits\ActorTrait;
use Illuminate\Support\Facades\Auth;

class CreateQuizQuestionService
{
    use ActorTrait;
    private $currentLevels;
    private $categoryPercentage = [];
    private $totalPercentage;
    private $categoriesId;
    private $selectedQuestions ;
    private $targetLevels;
    private $testCount ;


    public function __construct(Request $request)
    {


        $this->currentLevels = $request->currentLevels;
        $this->targetLevels = $request->targetLevels;
        $this->categoriesId = $request->categorySelected;
        $this->selectedQuestions = collect();
        $this->testCount = $request->testCount;


    }
    public function createQuestionsForQuiz()
    {
        $this->createCatgoryPercentage();
        $this->selectQuestionsBaseOnPercentage();

        return $this->selectedQuestions;
    }


    public function createCatgoryPercentage()
    {
       $this->fillCatgoriesPercentage();
       $this->setTotalPercentage();
       $this->NormilizeCatgoryPercentage();
    }

    public function fillCatgoriesPercentage()
    {
        foreach ($this->categoriesId as  $categoryId) {
            $category = CategoryQuestion::find($categoryId);
            if($category->descendants()->count() == 0)
            {
                $percentage = $this->targetLevels[$categoryId] - $this->currentLevels[$categoryId];
                $percentage = min($percentage, 100);
                $percentage = max($percentage, 0);
                $this->categoryPercentage[$categoryId] = $percentage;
            }
        }
    }

    public function setTotalPercentage()
    {
        $this->totalPercentage = 0;
        foreach ($this->categoryPercentage as  $categoryId => $percentage) {
            $this->totalPercentage += $percentage;
        }
        if($this->totalPercentage == 0)
        {
            $this->totalPercentage = 100;
        }
    }

    public function NormilizeCatgoryPercentage()
    {
        foreach ($this->categoryPercentage as  $categoryId => $percentage) {
            $this->categoryPercentage[$categoryId] = $percentage/$this->totalPercentage;  
        }

    }


    public function selectQuestionsBaseOnPercentage()
    {
        //select original question
        $totalQuestions = $this->testCount;
        $sumTargetLevel = 0;
        foreach ($this->categoryPercentage as $categoryId => $percentage) {
            $category = CategoryQuestion::find($categoryId);
            if(!$categoryId) continue;
            $numQuestions = $percentage * $totalQuestions;
            $current_level = $this->getUser()->categoryQuestions->find($categoryId)->pivot->level;
            $sumTargetLevel = $sumTargetLevel + $current_level;
            $questions =$category->questions()->test()->orderByRaw('ABS(percentage -?)' , $current_level)
                ->inRandomOrder()->limit($numQuestions)->get()->shuffle();
            $this->selectedQuestions = $this->selectedQuestions->merge($questions);
        }

        //select remaining question 
        $avgTargetlevel = $sumTargetLevel / (count($this->categoryPercentage));
        $questions = Question::whereIn("category_question_id", $this->categoriesId)->test()
            ->whereNotIn("id", $this->selectedQuestions->pluck("id"))
            ->orderByRaw('ABS(percentage -?)' , $avgTargetlevel)
            ->inRandomOrder()->limit($totalQuestions-$this->selectedQuestions->count())->get()->shuffle();

        $this->selectedQuestions = $this->selectedQuestions->merge($questions);
    }
}