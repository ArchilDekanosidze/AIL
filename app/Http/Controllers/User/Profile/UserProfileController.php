<?php

namespace App\Http\Controllers\User\Profile;

use App\Models\CategoryQuestion;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class UserProfileController extends Controller
{
    public function index()
    {
       return view("user.profile.profile");
    }
}
