<?php

namespace App\Http\Controllers\User;

use App\Models\CategoryQuestion;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class UserHomeController extends Controller
{
    public function index()
    {

        // $electronics0  = CategoryQuestion::create(['name' => 'دسته بندی']);
        $cat = CategoryQuestion::find(1);
        // $electronics  = Category::create(['name' => 'دوازدهم تجربی']);
        // $electronics1  = Category::create(['name' => 'ریاضی']);
        // $electronics2  = Category::create(['name' => 'فیزیک']);
        // $electronics3  = Category::create(['name' => 'شیمی']);

        // $electronics->appendToNode($electronics0)->save();
        // $electronics1->appendToNode($electronics)->save();
        // $electronics2->appendToNode($electronics)->save();
        // $electronics3->appendToNode($electronics)->save();

        // $shimi = Category::find(9);
        // $electronics1  = Category::create(['name' => 'فصل اول']);
        // $electronics2  = Category::create(['name' => 'فصل دوم']);

        // $electronics3  = CategoryQuestion::create(['name' => 'یونی']);
        // $cat = CategoryQuestion::find(9);
        // $electronics3->appendToNode($cat)->save();
        // $electronics2->appendToNode($shimi)->save();

        // $electronics3->appendToNode($shimi)->save();

        // $tree = Category::all()->toTree();
        // $decendant = $shimi->descendants;
        
        // $q = Question::create(["front"=> "f1", "back" => "b1", "p1" => "p1", "p2" => "p2",  "p3" => "p3",  "p4" => "p4", "answer" => 2 , "level" => 1 , "percentage" => 70.28, "count" => 102]);
        // $cat = CategoryQuestion::find(22); $q->categories()->attach($cat);
        // $q = Question::create(["front"=> "f2", "back" => "b1", "p1" => "p1", "p2" => "p2",  "p3" => "p3",  "p4" => "p4", "answer" => 2 , "level" => 1 , "percentage" => 70.28, "count" => 102]);
        // $cat = CategoryQuestion::find(23); $q->categories()->attach($cat);
        // $q = Question::create(["front"=> "f3", "back" => "b1", "p1" => "p1", "p2" => "p2",  "p3" => "p3",  "p4" => "p4", "answer" => 2 , "level" => 1 , "percentage" => 70.28, "count" => 102]);
        // $cat = CategoryQuestion::find(24); $q->categories()->attach($cat);
        // $q = Question::create(["front"=> "f4", "back" => "b1", "p1" => "p1", "p2" => "p2",  "p3" => "p3",  "p4" => "p4", "answer" => 2 , "level" => 1 , "percentage" => 70.28, "count" => 102]);        
        // $cat = CategoryQuestion::find(25); $q->categories()->attach($cat);
        // $q = Question::create(["front"=> "f5", "back" => "b1", "p1" => "p1", "p2" => "p2",  "p3" => "p3",  "p4" => "p4", "answer" => 2 , "level" => 1 , "percentage" => 70.28, "count" => 102]);
        // $cat = CategoryQuestion::find(26); $q->categories()->attach($cat);
        // $q = Question::create(["front"=> "f6", "back" => "b1", "p1" => "p1", "p2" => "p2",  "p3" => "p3",  "p4" => "p4", "answer" => 2 , "level" => 1 , "percentage" => 70.28, "count" => 102]);
        // $cat = CategoryQuestion::find(27); $q->categories()->attach($cat);
        // $q = Question::create(["front"=> "f7", "back" => "b1", "p1" => "p1", "p2" => "p2",  "p3" => "p3",  "p4" => "p4", "answer" => 2 , "level" => 1 , "percentage" => 70.28, "count" => 102]);
        // $cat = CategoryQuestion::find(28); $q->categories()->attach($cat);
        // $q = Question::create(["front"=> "f8", "back" => "b1", "p1" => "p1", "p2" => "p2",  "p3" => "p3",  "p4" => "p4", "answer" => 2 , "level" => 1 , "percentage" => 70.28, "count" => 102]);
        // $cat = CategoryQuestion::find(29); $q->categories()->attach($cat);  
        // $q = Question::create(["front"=> "f9", "back" => "b1", "p1" => "p1", "p2" => "p2",  "p3" => "p3",  "p4" => "p4", "answer" => 2 , "level" => 1 , "percentage" => 70.28, "count" => 102]);
        // $cat = CategoryQuestion::find(30); $q->categories()->attach($cat);


        // $question = Question::find(1);
        
        // dd($cat->descendantsAndSelf(1));
        // $q1->categories()->attach($yoni);

        // dd($yoni->descendants()->pluck('name')->toArray());
       
        // dd(CategoryQuestion::find(1)->allQuestion());

        $user = User::find(1);
        // $cat = CategoryQuestion::find(2);
        // $user->categoryQuestions()->attach($cat);
        // dd($user);

        
        $cat = CategoryQuestion::find(2);
        
        // dd($user->userCategoryStatus($cat));

        return view("home");
    }
}
