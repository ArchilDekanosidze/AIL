<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use Illuminate\Support\Facades\Hash;

class TestController extends Controller
{
    public function index()
    {
        $Questions = Question::all()->shuffle()->take(300);
        foreach ($Questions as $Question) {
            $Question->isfree = 1;
            $Question->save();
        }
    }
 
}
