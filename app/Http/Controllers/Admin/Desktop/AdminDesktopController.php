<?php
namespace App\Http\Controllers\Admin\Desktop;


use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use App\Http\Controllers\Controller;


class AdminDesktopController extends Controller
{

    public function index()
    {      
      return view('admin.desktop.desktop');
    }
  
}

