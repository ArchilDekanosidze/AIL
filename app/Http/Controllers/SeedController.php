<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use Illuminate\Support\Facades\Hash;

class SeedController extends Controller
{
    public function index()
    {
        $this->createUser();
        $this->createCategoryQuestion();
        $this->createQuestion();
    }
    public function createUser()
    {
        $user1  = User::create(['name' => 'حامد میرشکار' , 
                                'mobile' => '09120919921', 
                                'mobile_verified_at' => now(), 
                                'password' => Hash::make('123')]);
                                
    }

    public function createCategoryQuestion()
    {
        $cat0  = CategoryQuestion::create(['name' => 'دسته بندی']);

        $cat1  = CategoryQuestion::create(['name' => 'دوازدهم تجربی']);$cat1->appendToNode($cat0)->save();
        $cat11  = CategoryQuestion::create(['name' => 'ریاضی']);$cat11->appendToNode($cat1)->save();
        $cat12  = CategoryQuestion::create(['name' => 'فیزیک']);$cat12->appendToNode($cat1)->save();
        $cat13  = CategoryQuestion::create(['name' => 'شیمی']);$cat13->appendToNode($cat1)->save();

        $cat2  = CategoryQuestion::create(['name' => 'یازدهم تجربی']);$cat2->appendToNode($cat0)->save();
        $cat21  = CategoryQuestion::create(['name' => 'ریاضی']);$cat21->appendToNode($cat2)->save();
        $cat22  = CategoryQuestion::create(['name' => 'فیزیک']);$cat22->appendToNode($cat2)->save();
        $cat23  = CategoryQuestion::create(['name' => 'شیمی']);$cat23->appendToNode($cat2)->save();

        $cat3  = CategoryQuestion::create(['name' => 'دهم تجربی']);$cat3->appendToNode($cat0)->save();
        $cat31  = CategoryQuestion::create(['name' => 'ریاضی']);$cat31->appendToNode($cat3)->save();
        $cat32  = CategoryQuestion::create(['name' => 'فیزیک']);$cat32->appendToNode($cat3)->save();
        $cat33  = CategoryQuestion::create(['name' => 'شیمی']);$cat33->appendToNode($cat3)->save();

    }

    public function createQuestion()
    {
        $i = 0;
        $categoryQuestions = CategoryQuestion::all();
        foreach ($categoryQuestions as $categoryQuestion) { 
            $i++;
            $jMax= rand(1,300);
            $isfree = rand(1, 10);
            if($isfree >= 1)
            {
                $$isfree = 0;
            }
            for ($j=0; $j < $jMax; $j++) { 
                $percentage = rand(1,100);
                $q = Question::create(["category_question_id" => $categoryQuestion->id  , "front"=> "f". $categoryQuestion->name. "-".  $i . "-percentage = " . $percentage, "back" => "b". $i, "p1" => "p1" . $i, "p2" => "p2" . $i,  "p3" => "p3" . $i,  "p4" => "p4" . $i, "answer" => 2  , "percentage" =>$percentage, "count" => 102, "isfree" => $$isfree]);                
            }                 
        }
    }

    public function assignCategoryToUser()
    {
        $categoryQuestions = CategoryQuestion::all();
        $users = User::all();
        foreach ($users as $user) {
            foreach ($categoryQuestions as $categoryQuestion) {
                if(rand(1,10) > 9)
                {
                    $currentCategoryId = $categoryQuestion->id;
                    $categoriesId = CategoryQuestion::descendantsAndSelf($currentCategoryId)->pluck('id');
                    $data = $categoriesId->mapWithKeys(function($id){
                        return [$id => ['is_active' => true]];
                    })->toArray();
                    $user->categoryQuestions()->syncWithoutDetaching($data);
                }             
               
            }
        }
    }
}
