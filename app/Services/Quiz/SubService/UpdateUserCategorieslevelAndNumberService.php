<?php
namespace App\Services\Quiz\SubService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Traits\ActorTrait;
use Illuminate\Support\Facades\Auth;

class UpdateUserCategorieslevelAndNumberService
{
    use ActorTrait;
    private $numbers_to_change_level;
    private $targetLevels;
    private $existingData;


    public function __construct(Request $request)
    {
        // dd($request->all());
        $this->targetLevels = $request->targetLevels;
        $this->numbers_to_change_level = $request->numbers_to_change_level;
    }



    public function updateUserCategoriesData()
    {
        if(is_null($this->targetLevels)) return;
        $this->setExistingData();
        $this->updateUserCategoriesTargetLevel();
        $this->updateUserCategoriesNumberToChangeLevel();
    }

    public function updateUserCategoriesTargetLevel()
    {
        $data = [];
        foreach ($this->targetLevels as $categoryId => $targetLevel) {
            $targetLevel = min((int) $targetLevel, 100);
            $existing = $this->existingData[$categoryId]['target_level'] ?? null;    
            if ($existing === null || (int)$existing !== $targetLevel) {
                $data[] = [
                    'user_id' => $this->getUser()->id,
                    'category_question_id' => $categoryId,
                    'target_level' => $targetLevel
                ];
            }
        }
    
        if (!empty($data)) {
            DB::table('user_category_question')->upsert($data, ['user_id', 'category_question_id']);
        }

    }

    public function updateUserCategoriesNumberToChangeLevel()
    {      
        $data = [];
        foreach ($this->numbers_to_change_level as $categoryId => $numberToChange) {
            $numberToChange = (int) $numberToChange;
            $existing = $this->existingData[$categoryId]['number_to_change_level'] ?? null;    
            if ($existing === null || (int)$existing !== $numberToChange) {
                $data[] = [
                    'user_id' => $this->getUser()->id,
                    'category_question_id' => $categoryId,
                    'number_to_change_level' => $numberToChange
                ];
            }
        }
    
        if (!empty($data)) {
            DB::table('user_category_question')->upsert($data, ['user_id', 'category_question_id']);
        }
    }

    public function setExistingData()
    {
        $userId = $this->getUser()->id;
        $this->existingData = DB::table('user_category_question')
            ->where('user_id', $userId)
            ->whereIn('category_question_id', array_unique(array_merge(
                array_keys($this->targetLevels), 
                array_keys($this->numbers_to_change_level)
            )))
            ->get()
            ->keyBy('category_question_id')
            ->map(function ($item) {
                return [
                    'target_level' => (int) $item->target_level,
                    'number_to_change_level' => (int) $item->number_to_change_level,
                ];
            });
    }
    
}
