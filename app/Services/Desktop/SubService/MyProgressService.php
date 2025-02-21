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


    public function __construct(Request $request)
    {

        $this->request = $request;

    }

    public function getProgressData()
    {
        $parentCategoryId = 6;
        $parentCategoryId = $this->request->parentCategoryId;
        $OriginalParentCategory = CategoryQuestion::find($parentCategoryId);
        $allCategoriesId = CategoryQuestion::withDepth()->where('parent_id', $parentCategoryId)->get()->sortBy('_lft')->pluck("id")->toArray();
        $userCategories = $this->user->categoryQuestions()->whereIn("category_question_id", $allCategoriesId)->get()->sortBy('lft');
        
        $ids = $userCategories->pluck('id')->toArray();
        $labels = $userCategories->pluck('name')->toArray();
        $levels = $userCategories->pluck('pivot.level')->toArray();
        $target_levels = $userCategories->pluck('pivot.target_level')->toArray();
        $histories = $userCategories->pluck('pivot.history')->toArray();


        $allTimes = [];
        foreach ($histories as $history) {
            $history = json_decode($history, true);
            foreach ($history as $cell) {
                $time = $cell['time'];
                $time = (new Verta($time))->formatDate();
                $allTimes[] = $time;
            }
        }
        $allTimes = array_unique($allTimes);
        sort($allTimes);
        $level_histories = [];
        foreach ($histories as $history) {
            $level_histories[] = array_fill(0, count($allTimes), null);
        }
        for ($i=0; $i<count($histories) ; $i++) {
            $history = $histories[$i];
            $history = json_decode($history, true);
            foreach ($history as $cell) {
                $time = $cell['time'];
                $time = (new Verta($time))->formatDate();
                $index = array_search($time, $allTimes);
                if($index>=0)
                {
                    $level_histories[$i][$index] = $cell['level'];
                }
            }
        }
        for ($i=0; $i<count($level_histories) ; $i++) {
            $level_histories_raw = $level_histories[$i];
            for ($j=1; $j<count($level_histories_raw); $j++) {
                if($level_histories[$i][$j] === null)
                {
                    $level_histories[$i][$j] = $level_histories[$i][$j-1];
                }
            }
        }





        $data = [
            'ids' => $ids,
            'labels' => $labels,
            'levels' => $levels,
            'target_levels' => $target_levels,
            'level_history' => $level_histories,
            'level_history_times' => $allTimes,
            'OriginalParentCategoryId' => $OriginalParentCategory->parent_id,
            "ParentCategoryName" => $OriginalParentCategory->name
        ];
        

        if($ids == null)
        {
            $userCategory = $this->user->categoryQuestions()->where("category_question_id", $parentCategoryId)->first();

            $new_level_history_time_array = [];
            $level_history_time = $userCategory->pivot->level_history_time;
            $level_history_time_array = explode(",", $level_history_time);
            foreach ($level_history_time_array as $time) {
                $time = (new Verta($time))->formatDate();
                $new_level_history_time_array[] = $time;
            } 
            $level_history = explode("," , $userCategory->pivot->level_history);
            $data = [
                'ids' => [$userCategory->id],
                'labels' => $userCategory->name,
                'levels' => $userCategory->pivot->level,
                'target_levels' => $userCategory->pivot->target_level,
                'level_history' => $level_history,
                'level_history_times' => $new_level_history_time_array,
                'OriginalParentCategoryId' => $OriginalParentCategory->parent_id,
                "ParentCategoryName" => $OriginalParentCategory->name
            ];            
        }
        // dd($data);
        return $data;
    }

}