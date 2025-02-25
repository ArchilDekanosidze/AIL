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
        return view("home");
    }
}
