<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Comment;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as faker;

class SeedController extends Controller
{
    public function index() 
    {
        $this->createUser();
        $this->createCategoryQuestion();
        // $this->createQuestion();
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
        $cat2  = CategoryQuestion::create(['name' => 'یازدهم تجربی']);$cat2->appendToNode($cat0)->save();
        $cat3  = CategoryQuestion::create(['name' => 'دهم تجربی']);$cat3->appendToNode($cat0)->save();
        $cat4  = CategoryQuestion::create(['name' => 'دوازدهم ریاضی']);$cat4->appendToNode($cat0)->save();
        $cat5  = CategoryQuestion::create(['name' => 'یازدهم ریاضی']);$cat5->appendToNode($cat0)->save();
        $cat6  = CategoryQuestion::create(['name' => 'دهم ریاضی']);$cat6->appendToNode($cat0)->save();
        $cat7  = CategoryQuestion::create(['name' => 'دوازدهم انسانی']);$cat7->appendToNode($cat0)->save();
        $cat8  = CategoryQuestion::create(['name' => 'یازدهم انسانی']);$cat8->appendToNode($cat0)->save();
        $cat9  = CategoryQuestion::create(['name' => 'دهم انسانی']);$cat9->appendToNode($cat0)->save();
        $cat10  = CategoryQuestion::create(['name' => 'دوازدهم معارف اسلامی']);$cat10->appendToNode($cat0)->save();
        $cat11  = CategoryQuestion::create(['name' => 'یازدهم معارف اسلامی']);$cat11->appendToNode($cat0)->save();
        $cat12  = CategoryQuestion::create(['name' => 'دهم معارف اسلامی']);$cat12->appendToNode($cat0)->save();
        $cat13  = CategoryQuestion::create(['name' => 'نهم']);$cat13->appendToNode($cat0)->save();
        $cat14  = CategoryQuestion::create(['name' => 'هشتم']);$cat14->appendToNode($cat0)->save();
        $cat15  = CategoryQuestion::create(['name' => 'هفتم']);$cat15->appendToNode($cat0)->save();
        $cat16  = CategoryQuestion::create(['name' => 'ششم']);$cat16->appendToNode($cat0)->save();
        $cat17  = CategoryQuestion::create(['name' => 'پنجم']);$cat17->appendToNode($cat0)->save();
        $cat18  = CategoryQuestion::create(['name' => 'چهارم']);$cat18->appendToNode($cat0)->save();
        $cat19  = CategoryQuestion::create(['name' => 'سوم']);$cat19->appendToNode($cat0)->save();
        $cat20  = CategoryQuestion::create(['name' => 'دوم']);$cat20->appendToNode($cat0)->save();
        $cat21  = CategoryQuestion::create(['name' => 'اول']);$cat21->appendToNode($cat0)->save();


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


    public function createComment()
    {
        $userId = 1;
        $catId = 205;
        $cat = CategoryQuestion::find($catId);
        $allQuestions = $cat->allQuestion();
        foreach ($allQuestions as $question) {
            $questionId = $question->id;
            for ($i=0; $i < 100; $i++) {   
                if(Comment::where('question_id', $questionId)->first())
                {
                    $parentId = (rand(1,100)<= 80) ? Comment::where('question_id', $questionId)->inRandomOrder()->first()->id : null;
                }
                else
                {
                    $parentId =  null;

                }          
                $faker = Faker::create();
                $body = $faker->sentence();
                $comment = new Comment();
                $comment->user_id = $userId;
                $comment->question_id = $questionId;
                $comment->parent_id = $parentId;
                $comment->body = $body;
                $comment->save();
            }
        }
    }
}
