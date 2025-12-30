<?php
namespace App\Services\Quiz\SubService;

use Carbon\Carbon;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\CategoryQuestion;
use Illuminate\Support\Facades\DB;
use App\Services\Traits\ActorTrait;
use Illuminate\Support\Facades\Auth;
use App\Services\Quiz\Traits\QuizTrait;
use App\Services\Traits\HistoryFileTrait;


class SaveQuizDataService
{
    use QuizTrait, ActorTrait, HistoryFileTrait;
    public $data;
    private $allcategoryQuestions;
    private $userCategoryQuestions;


    public function __construct()
    {
    }

    public function saveQuizData(Quiz $quiz)
    {

        
        if($quiz->status == "ended")
        {
            return;
        }
        $rightAnswers = 0;
        $wrongAnswers = 0;
        $notAnswers = 0;
        $quizQuestions = $quiz->quizQuestions;
        $this->userCategoryQuestions = $this->getUser()->categoryQuestions;
        foreach ($quizQuestions as $quizQuestion) 
        {
            if($quizQuestion->user_answer == 0 || $quizQuestion->user_answer == null)
            {
                $notAnswers++;
            } 
            else
            {
                $question = Question::find($quizQuestion->question_id);
                if($quizQuestion->user_answer == $question->answer)
                {
                    $rightAnswers++;
                    $this->changeQuestionAndUserCategoryQuestion(1, $question);
                }
                else
                {
                    $wrongAnswers++;     
                    $this->changeQuestionAndUserCategoryQuestion(0, $question);
                }
            }
        }
        // dd($this->data);
        if($this->data)
        {
            // $this->updateParentCatLevel(); توی کد جدیدی که نوشتم اومدم پرنت رو بر اساس هر سوال آپدیت کردم. در این صورت دیگه نیازی نیست کد جدایی برای والدین بنویسیم که اگر بنویسیم هم اعدادش متفاوت میشه
            // dd($this->data);
            foreach ($this->data as $key => $value) {
                $history = $this->getHistory($key);
                // dd($history);
            }
            // dd($this->data);
            $this->getUser()->categoryQuestions()->syncWithoutDetaching($this->data);
        }

        
        $quiz->rightAnswers = $rightAnswers;
        $quiz->wrongAnswers = $wrongAnswers;
        $quiz->notAnswers = $notAnswers;
        $quiz->finalPercentage =floor(($rightAnswers*3 - $wrongAnswers)*100 /($quiz->count*3));
        $quiz->status = 'ended';

        $quiz->save();
        
    }

    public function changeQuestionAndUserCategoryQuestion($isCorrect, $question)
    {
        $this->changeQuestion($isCorrect, $question);

        $categoriesId = $this->questionAncestorsAndSelfId($question);
        $categoriesQuestion = $this->getUser()->categoryQuestions()->with('directChildren')->whereIn("category_questions.id", $categoriesId)->get();

        // dd($categoriesQuestion);
                // ->withCount('children') // This will load the number of direct children for each category


        $this->updatecategoriesQuestion($categoriesQuestion, $isCorrect, $question);  


    }

    public function changeQuestion($isCorrect, $question)
    {
        if($isCorrect) 
        {
            $question->percentage =max(($question->percentage * $question->count -1)/$question->count , 1);
        }
        else
        {
            $question->percentage =min( ($question->percentage * $question->count +1)/($question->count), 100 );
        }
        $question->count = $question->count +1;
        $question->save();
    }

    public function updatecategoriesQuestion($categoriesQuestion, $isCorrect, $question)
    {
       

        foreach ($categoriesQuestion as $categoryQuestion)
        {                                    
            $newLevel = $this->newHistory($categoryQuestion, $isCorrect);
            $bridgeId = $categoryQuestion->pivot->id;
            $this->data[$categoryQuestion->id] = ['level' =>  $newLevel];            
        }
    }

    // new newlevel
    public function newHistory($categoryQuestion, $isCorrect)
    {
        $this->setInitialData($categoryQuestion);

        $bridgeId = $categoryQuestion->pivot->id;
        if($this->getHistory($bridgeId) != null)
        {
            $oldHistory = $this->getHistory($bridgeId);
            foreach ($oldHistory as $old) {
                $history[] = $old;
            }
        }
        $newLevel = $this->newlevel($categoryQuestion, $history, $isCorrect);
        $history[] = ["level" => $newLevel, "time" => now()->timestamp, "isCorrect" => $isCorrect ? 1 : 0];
        $this->saveHistory($bridgeId, $history);
        return $newLevel;
    }
    // old new level

    // public function newHistory($categoryQuestion, $isCorrect)
    // {
    //     $this->setInitialData($categoryQuestion);

    //     $bridgeId = $categoryQuestion->pivot->id;
    //     if($this->getHistory($bridgeId) != null)
    //     {
    //         $oldHistory = $this->getHistory($bridgeId);
    //         foreach ($oldHistory as $old) {
    //             $history[] = $old;
    //         }
    //     }
        
    //     $history[] = ["level" => null, "time" => now()->timestamp, "isCorrect" => $isCorrect ? 1 : 0];
    //     $newLevel = $this->newlevel($categoryQuestion, $history);
    //     $history[count($history) - 1]['level'] = $newLevel;        
    //     // $result["level"] = $newLevel;
    //     // $result["history"] = $history;
    //     $this->saveHistory($bridgeId, $history);
    //     // dump($newLevel);
    //     return $newLevel;
    // }


    // new newLevel
    public function newlevel($categoryQuestion, $history, $isCorrect)
    {
        try {
            $answerHistory =array_map(fn($item) => $item['isCorrect'], $history);
            //code...
        } catch (\Throwable $th) {
            dd($history);
            //throw $th;
        }
        if(count($categoryQuestion->directChildren) > 0)
        {

            $totla_question_count = 0;
            $sumLevelCount = 0;

            $subCats = $categoryQuestion->directChildren;
            foreach ($subCats as $subCat) {
                $level = $this->userCategoryQuestions->where('id', $subCat->id)->first();

                // in code ro ezafe kardam ke onhai ke to zir majmoe nist to miangiri hazf beshan
                if(!$level)
                {
                       continue;
                }               
                $question_count = $subCat->question_count;

                if($level)
                {
                    if(isset($this->data[$subCat->id]))
                    {
                        $level = $this->data[$subCat->id]['level'];
                    }
                    else
                    {
                        $level = $level->pivot->level;
                    }
                }
                else
                {
                    $level = 1;
                }
                $totla_question_count += $question_count;
                $sumLevelCount += $level * $question_count;
            }

            
            $newLevel =  $sumLevelCount / $totla_question_count;

        }
        else
        {
            $currentLevel = $this->getCurrentLevel($categoryQuestion);
            if($isCorrect)
            {
                $newLevel = $currentLevel + 3 /10 * 100/$categoryQuestion->pivot->number_to_change_level ;
            }    
            else
            {
                $newLevel = $currentLevel  - 1 / 10 *  100/$categoryQuestion->pivot->number_to_change_level ;
            }
        }
        // dd(2);
        $newLevel = min(100, $newLevel);
        $newLevel = max(1, $newLevel);
        return $newLevel;
    }


    private function getCurrentLevel($categoryQuestion)
    {
        return $this->data[$categoryQuestion->id]['level']
            ?? $categoryQuestion->pivot->level;
    }


    // old new level
    // public function newlevel($categoryQuestion, $history)
    // {
    //     try {
    //         $answerHistory =array_map(fn($item) => $item['isCorrect'], $history);
    //         //code...
    //     } catch (\Throwable $th) {
    //         dd($history);
    //         //throw $th;
    //     }
    //     if(count($categoryQuestion->directChildren) > 0)
    //     {

    //         $totla_question_count = 0;
    //         $sumLevelCount = 0;

    //         $subCats = $categoryQuestion->directChildren;
    //         foreach ($subCats as $subCat) {
    //             $level = $this->userCategoryQuestions->where('id', $subCat->id)->first();

    //             // in code ro ezafe kardam ke onhai ke to zir majmoe nist to miangiri hazf beshan
    //             if(!$level)
    //             {
    //                    continue;
    //             }               
    //             $question_count = $subCat->question_count;

    //             if($level)
    //             {
    //                 if(isset($this->data[$subCat->id]))
    //                 {
    //                     $level = $this->data[$subCat->id]['level'];
    //                 }
    //                 else
    //                 {
    //                     $level = $level->pivot->level;
    //                 }
    //             }
    //             else
    //             {
    //                 $level = 1;
    //             }
    //             $totla_question_count += $question_count;
    //             $sumLevelCount += $level * $question_count;
    //         }

            
    //         $newLevel =  $sumLevelCount / $totla_question_count;

    //     }
    //     else
    //     {
    //         $newerAnswerHistory = array_slice($answerHistory, -$categoryQuestion->pivot->number_to_change_level);
    //         $sumAnswerForLevel = 0;
    //         foreach ($newerAnswerHistory as $answer) {
    //             if($answer == 1)
    //             {
    //                 $sumAnswerForLevel = $sumAnswerForLevel + 3;
    //             }
    //             else if($answer == 0)
    //             {
    //                 $sumAnswerForLevel = $sumAnswerForLevel -1;
    //             }
    //         }
    //         $newLevel =(int) ($sumAnswerForLevel / ($categoryQuestion->pivot->number_to_change_level*3) * 100);

    //         try {
    //             $newerFullHistory = array_slice($history, -$categoryQuestion->pivot->number_to_change_level);                             
    //             $firstTime = $newerFullHistory[0]['time'];
    //             $firstTime = Carbon::createFromTimestamp($firstTime);
    //             $daysPassed = now()->diffInDays($firstTime);
    //             $decay = $categoryQuestion->pivot->decay;
    //             $levelDecay = $daysPassed*$decay;
    //             $newLevel = $newLevel - $levelDecay;
    //         }
    //         catch (\Throwable $th) {
    //             //throw $th;
    //         }

    //         $newLevel = min(100, $newLevel);
    //         $newLevel = max(1, $newLevel);
    //     }
    //     // dd(2);
    //     return $newLevel;
    // }

    public function setInitialData($categoryQuestion)
    {
        $isSetData = isset($this->data[$categoryQuestion->id]);
        if($isSetData)
        {
            return;
        }
        $this->data[$categoryQuestion->id]['level'] = $categoryQuestion->pivot->level ;
    }

    public function updateParentCatLevel()
    {
        // dd($this->data);

        $categoryIds = array_keys($this->data); // e.g., [3462, 22, 3460, 3461]
    
        // Fetch all category questions and calculate descendant range
        $this->allcategoryQuestions = CategoryQuestion::query()
        ->whereIn('id', $categoryIds)
        ->withCount('children') // This will load the number of direct children for each category
        ->get()
        ->keyBy('id');
    
        // Loop over each category in the data
        foreach ($this->allcategoryQuestions as $categoryQuestion) {            
            if ($categoryQuestion->children_count == 0) {
               $this->updateParentsLevel($categoryQuestion);
            }
        }

        // After this loop, $this->data will have the updated levels for both direct categories and their parents
    }
    
    public function calculateLevelBasedOnQuestionCount($totalQuestionCount)
    {
        // Example: level increases as total question count increases.
        // This can be adjusted to your level calculation logic.
        $newLevel = (int) min(100, max(1, ($totalQuestionCount / 10))); // Example logic
        return $newLevel;
    }

    public function updateParentsLevel($categoryQuestion)
    {

        $parentsId = $categoryQuestion->ancestors()->select('id', 'parent_id')->get()->pluck('id')->toArray();
        array_shift($parentsId);
        $parentsId = array_reverse($parentsId);
        foreach ($parentsId as $parentId) {
            $totla_question_count = 0;
            $sumLevelCount = 0;

            $subCats = CategoryQuestion::where('parent_id', $parentId)->get();
            foreach ($subCats as $subCat) {
                $question_count = $subCat->question_count;
                $level = $this->userCategoryQuestions->where('id', $subCat->id)->first();
                if($level)
                {
                    if(isset($this->data[$subCat->id]))
                    {
                        $level = $this->data[$subCat->id]['level'];
                    }
                    else
                    {
                        $level = $level->pivot->level;
                    }
                }
                else
                {
                    $level = 1;
                }
                $totla_question_count += $question_count;
                $sumLevelCount += $level * $question_count;
            }
            
            $this->data[$parentId]['level'] =  $sumLevelCount / $totla_question_count;
            if($parentId == 22)
            {
                // dd($totla_question_count, $sumLevelCount,  $sumLevelCount / $totla_question_count);
            }
        }       
    }


    
}