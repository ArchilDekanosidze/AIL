<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Comment;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use App\Http\Controllers\Controller;


class AdminSiteInfoController extends Controller
{
    public function users()
    {
        $users = User::all();
        dd($users->pluck('name'), $users);
    }
   

}

