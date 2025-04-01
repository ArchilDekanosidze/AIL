<?php
namespace App\Http\Controllers\Profile;



use App\Services\Quiz\QuizService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\Desktop\DesktopService;
use App\Services\Traits\ActorControllerTrait;
use App\Services\Desktop\ControllerTraits\quizListControllerTrait;

class ProfileStudentController extends Controller
{


    
    public function index(User $user)
    {
       
        return view("profile.student.profile", compact("user"));
    }


}
