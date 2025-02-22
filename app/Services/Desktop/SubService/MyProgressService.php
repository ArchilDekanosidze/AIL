<?php
namespace App\Services\Desktop\SubService;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Hekmatinasser\Verta\Verta;
use App\Models\CategoryQuestion;
use App\Services\Traits\ActorTrait;
use Illuminate\Support\Facades\Auth;


class MyProgressService
{
    use ActorTrait;
    private $request;
    private $parentCategoryId;
    private $OriginalParentCategory;
    private $userCategories;
    private $ids;
    private $labels;
    private $levels;
    private $target_levels;
    private $histories;
    private $allTimes=[];
    private $level_histories =[];


    public function __construct(Request $request)
    {

        $this->request = $request;

    }

    public function getProgressData()
    {
        $this->getUserCategories();
        $this->setInitialData();
        $this->setAllTimes();
        $this->createEmptyLevelHistory();
        $this->fillLevelHistory();
        $this->fillNullValueInLevelHistory();
        if($this->ids )
        {
            $data = $this->createDataArray();
        }
        else
        {
            $data = $this->createDataSingle();
        }
        return $data;
    }

    public function getUserCategories()
    {
        // $this->parentCategoryId = 7;
        $this->parentCategoryId = $this->request->parentCategoryId;
        $this->OriginalParentCategory = CategoryQuestion::find($this->parentCategoryId);
        $allCategoriesId = CategoryQuestion::withDepth()->where('parent_id', $this->parentCategoryId)->get()->sortBy('_lft')->pluck("id")->toArray();
        $this->userCategories = $this->user->categoryQuestions()->whereIn("category_question_id", $allCategoriesId)->get()->sortBy('lft');
    }

    public function setInitialData()
    {
        $this->ids = $this->userCategories->pluck('id')->toArray();
        $this->labels = $this->userCategories->pluck('name')->toArray();
        $this->levels = $this->userCategories->pluck('pivot.level')->toArray();
        $this->target_levels = $this->userCategories->pluck('pivot.target_level')->toArray();
        $this->histories = $this->userCategories->pluck('pivot.history')->toArray();
    }

    public function setAllTimes() {
        foreach ($this->histories as $history) {
            $history = json_decode($history, true);
            foreach ($history as $cell) {
                $time =$this->convertTime($cell['time']);
                $this->allTimes[] = $time;
            }
        }
        $this->allTimes = array_unique($this->allTimes);
        sort($this->allTimes);
    }

    public function createEmptyLevelHistory()
    {
        foreach ($this->histories as $history) {
            $this->level_histories[] = array_fill(0, count($this->allTimes), null);
        }
    }

    public function fillLevelHistory()
    {
        for ($i=0; $i<count($this->histories) ; $i++) {
            $history = $this->histories[$i];
            $history = json_decode($history, true);
            $flag  = true;
            $prevIndex = -1;
            $tempLevel =[];
            foreach ($history as $cell) {
                $time =$this->convertTime($cell['time']);
                $index = array_search($time, $this->allTimes);
                if($index>=0)
                {
                    if($flag)
                    {
                        $prevIndex = $index;
                        $flag = false;
                    }
                    $this->level_histories[$i][$index] = $cell['level'];
                    if($prevIndex != $index)
                    {
                        if(count($tempLevel)>0)
                        {
                            $avgLeve = array_sum($tempLevel)/count($tempLevel);
                        }
                        else
                        {
                            $avgLeve = $cell['level'];
                        }
                        $tempLevel =[];
                        $tempLevel[] =  $cell['level'];
                        $this->level_histories[$i][$prevIndex] = $avgLeve;
                        $prevIndex = $index;
    
                    }
                    else
                    {
                        $tempLevel[] =  $cell['level'];
                    }
                }
            }
        }
    }

    public function fillNullValueInLevelHistory()
    {
        for ($i=0; $i<count($this->level_histories) ; $i++) {
            $level_histories_raw = $this->level_histories[$i];
            for ($j=1; $j<count($level_histories_raw); $j++) {
                if($this->level_histories[$i][$j] === null)
                {
                    $this->level_histories[$i][$j] = $this->level_histories[$i][$j-1];
                }
            }
        }
    }
    public function createDataArray()
    {
        $data = [
            'ids' => $this->ids,
            'labels' => $this->labels,
            'levels' => $this->levels,
            'target_levels' => $this->target_levels,
            'level_history' => $this->level_histories,
            'level_history_times' => $this->allTimes,
            'OriginalParentCategoryId' => $this->OriginalParentCategory->parent_id,
            "ParentCategoryName" => $this->OriginalParentCategory->name
        ];
        return $data;
    }

    public function createDataSingle()
    {
        $userCategory = $this->user->categoryQuestions()->where("category_question_id", $this->parentCategoryId)->first();
        $histories = $userCategory->pivot->history;
        $histories = json_decode($histories, true);
        $allTimes = [];
        foreach ($histories as $history) {
           $time =$this->convertTime($history['time']);
           $allTimes[] = $time;
        }

        $allTimes = array_unique($allTimes);
        sort($allTimes);
        
        $level_history = array_fill(0, count($allTimes), null);

        $prevIndex = 0;
        $tempLevel =[];
        foreach ($histories as $history) {
            $time = $this->convertTime($history['time']);
            $index = array_search($time, $allTimes);
            if($index>=0)
            {
                $level_history[$index] = $history['level'];
                if($prevIndex != $index)
                {
                    if(count($tempLevel)>0)
                    {
                        $avgLeve = array_sum($tempLevel)/count($tempLevel);
                    }
                    else
                    {
                        $avgLeve = $history['level'];
                    }
                    $tempLevel =[];
                    $tempLevel[] =  $history['level'];
                    $level_history[$prevIndex] = $avgLeve;
                    $prevIndex = $index;

                }
                else
                {
                    $tempLevel[] =  $history['level'];
                }
            }           
        }
        

      
        $data = [
            'ids' => [$userCategory->id],
            'labels' => $userCategory->name,
            'levels' => $userCategory->pivot->level,
            'target_levels' => $userCategory->pivot->target_level,
            'level_history' => $level_history,
            'level_history_times' => $allTimes,
            'OriginalParentCategoryId' => $this->OriginalParentCategory->parent_id,
            "ParentCategoryName" => $this->OriginalParentCategory->name
        ];   

        return $data;
    }

    public function convertTime($stringTime)
    {
        $time = (new Verta($stringTime))->format("Y/m/d-H:i");

        return $time;
    }
}