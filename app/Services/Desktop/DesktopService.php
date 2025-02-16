<?php
namespace App\Services\Desktop;

use Illuminate\Http\Request;
use Hekmatinasser\Verta\Verta;
use App\Models\CategoryQuestion;
use Illuminate\Support\Facades\Auth;

class DesktopService
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
        $OriginalParentCategory = CategoryQuestion::find($parentCategoryId);
        $allCategoriesId = CategoryQuestion::withDepth()->where('parent_id', $parentCategoryId)->get()->sortBy('_lft')->pluck("id")->toArray();
        $userCategories = $this->user->categoryQuestions()->whereIn("category_question_id", $allCategoriesId)->get()->sortBy('lft');
        $ids = $userCategories->pluck('id')->toArray();
        $labels = $userCategories->pluck('name')->toArray();
        $levels = $userCategories->pluck('pivot.level')->toArray();
        $target_levels = $userCategories->pluck('pivot.target_level')->toArray();
        $main_level_histories = $userCategories->pluck('pivot.level_history')->toArray();
        $main_level_history_times = $userCategories->pluck('pivot.level_history_time')->toArray();
        
        $new_main_level_histories = [];
        foreach ($main_level_histories as $main_level_history) {
            $new_main_level_histories [] = explode("," ,$main_level_history);
        }

        $new_main_level_history_times = [];

        foreach ($main_level_history_times as $main_level_history_time) {
            $level_history_time_array = explode(",", $main_level_history_time);
            $new_level_history_time_array = [];
            foreach ($level_history_time_array as $time) {
                $time = (new Verta($time))->formatDate();
                $new_level_history_time_array[] = $time;
            } 
            $new_main_level_history_times[] =  $new_level_history_time_array;
        }
            

        $data = [
            'ids' => $ids,
            'labels' => $labels,
            'levels' => $levels,
            'target_levels' => $target_levels,
            'level_history' => $new_main_level_histories,
            'level_history_times' => $new_main_level_history_times,
            'OriginalParentCategoryId' => $OriginalParentCategory->parent_id,
            "ParentCategoryName" => $OriginalParentCategory->name
        ];

        return $data;
    }

}
