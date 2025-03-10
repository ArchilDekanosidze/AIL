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


    public function __construct(Request $request)
    {
        $this->targetLevels = $request->targetLevels;
        $this->numbers_to_change_level = $request->numbers_to_change_level;
    }



    public function updateUserCategoriesData()
    {
       
        $this->updateUserCategoriesTargetLevel();
        $this->updateUserCategoriesNumberToChangeLevel();
    }

    public function updateUserCategoriesTargetLevel()
    {
        // $data = [];
        // foreach ($this->targetLevels  as $categoryId => $targetLevel) {
        //     $data[$categoryId] = ['target_level' => min($targetLevel, 100)];
        // }        
        // $this->getUser()->categoryQuestions()->syncWithoutDetaching($data);


        $data = [];
        foreach ($this->targetLevels  as $categoryId => $targetLevel) {
            $data[] = ['user_id' => $this->getUser()->id, 'category_question_id' => $categoryId, 'target_level' => min($targetLevel, 100)];
        }  
        DB::table('user_category_question')->upsert($data, ['user_id', 'category_question_id']);

    }

    public function updateUserCategoriesNumberToChangeLevel()
    {
        // $data = [];
        // foreach ($this->numbers_to_change_level  as $categoryId => $number_to_change_level) {
        //     $data[$categoryId] = ["number_to_change_level" => $number_to_change_level];
        // }        
        // $this->getUser()->categoryQuestions()->syncWithoutDetaching($data);



        $data = [];
        foreach ($this->numbers_to_change_level  as $categoryId => $number_to_change_level) {
            $data[] = ['user_id' => $this->getUser()->id, 'category_question_id' => $categoryId, 'number_to_change_level' => $number_to_change_level];
        }        
        DB::table('user_category_question')->upsert($data, ['user_id', 'category_question_id']);



    }
    
}
