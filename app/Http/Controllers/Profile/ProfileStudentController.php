<?php
namespace App\Http\Controllers\Profile;



use App\Models\User;
use Illuminate\Http\Request;
use App\Services\Quiz\QuizService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Desktop\DesktopService;
use App\Services\Traits\ActorControllerTrait;
use App\Services\Desktop\ControllerTraits\quizListControllerTrait;

class ProfileStudentController extends Controller
{


     
    public function index(User $user)
    {      
        $user->load('freeBadges'); // Load badges and pivot (badge, score)
        $badges = $user->freeBadges->sortByDesc(fn($b) => $b->pivot->score);
        return view("profile.student.profile", compact("user", "badges"));
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048'
        ]);

        $path = $request->file('avatar')->store('avatars', 'public');

        auth()->user()->update(['avatar' => basename($path)]);

        return back()->with('success', 'آواتار با موفقیت آپلود شد');
    }



}
