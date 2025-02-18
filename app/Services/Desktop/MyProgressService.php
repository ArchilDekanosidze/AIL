<?php
namespace App\Services\Desktop;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Hekmatinasser\Verta\Verta;
use App\Models\CategoryQuestion;
use Illuminate\Support\Facades\Auth;


class MyProgressService
{
    private $request;
    private $user;


    public function __construct(Request $request)
    {
        Auth::loginUsingId(1, TRUE);     

        $this->request = $request;
        $this->user = auth()->user();

    }

    public function getProgressData()
    {
        $parentCategoryId = $this->request->parentCategoryId;
        // $parentCategoryId = 8;
        $OriginalParentCategory = CategoryQuestion::find($parentCategoryId);
        $allCategoriesId = CategoryQuestion::withDepth()->where('parent_id', $parentCategoryId)->get()->sortBy('_lft')->pluck("id")->toArray();
        $userCategories = $this->user->categoryQuestions()->whereIn("category_question_id", $allCategoriesId)->get()->sortBy('lft');
        $ids = $userCategories->pluck('id')->toArray();
        $labels = $userCategories->pluck('name')->toArray();
        $levels = $userCategories->pluck('pivot.level')->toArray();
        $target_levels = $userCategories->pluck('pivot.target_level')->toArray();
        $main_level_histories = $userCategories->pluck('pivot.level_history')->toArray();
        $main_level_history_times = $userCategories->pluck('pivot.level_history_time')->toArray();

        
        $two_dimention_level_histories = [];
        foreach ($main_level_histories as $main_level_history) {
            $two_dimention_level_histories [] = explode("," ,$main_level_history);
        }

        $two_dimention_level_history_times = [];

        foreach ($main_level_history_times as $main_level_history_time) {
            $level_history_time_array = explode(",", $main_level_history_time);
            $new_level_history_time_array = [];
            foreach ($level_history_time_array as $time) {
                $time = (new Verta($time))->formatDate();
                $new_level_history_time_array[] = $time;
            } 
            $two_dimention_level_history_times[] =  $new_level_history_time_array;
        }
        $linear_level_history_times =array_values(Arr::sort(array_unique(Arr::flatten($two_dimention_level_history_times))));
        $updated_Two_dimention_level_history = [];

        for($i=0; $i<  count($two_dimention_level_history_times) ; $i++) {
            $prev = null;
            for ($j=0; $j < count($linear_level_history_times) ; $j++) { 
                $index = array_search($linear_level_history_times[$j],$two_dimention_level_history_times[$i]);            
                if($index != false)
                {
                    $prev = $two_dimention_level_histories[$i][$index];
                }

                $updated_Two_dimention_level_history[$i][$j] = $prev;                                     
            }

        }


        $data = [
            'ids' => $ids,
            'labels' => $labels,
            'levels' => $levels,
            'target_levels' => $target_levels,
            'level_history' => $updated_Two_dimention_level_history,
            'level_history_times' => $linear_level_history_times,
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