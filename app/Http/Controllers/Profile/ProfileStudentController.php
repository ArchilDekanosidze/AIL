<?php
namespace App\Http\Controllers\Profile;



use App\Models\User;
use Illuminate\Http\Request;
use App\Services\Quiz\QuizService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile\UserRelationship;
use App\Services\Desktop\DesktopService;
use App\Services\Traits\ActorControllerTrait;
use App\Models\Profile\UserRelationshipRequest;
use App\Services\Desktop\ControllerTraits\quizListControllerTrait;

class ProfileStudentController extends Controller
{


     
    public function index(User $user)
    {      
        $authUser = auth()->user();

        $relationshipStatus = null;
        $incomingRequests = null;
        $mySupervisors = null;
        $myStudents = null;


        if ($authUser->id !== $user->id) {
            // Find any existing request between these users
            $existingRequest = UserRelationshipRequest::where(function ($q) use ($authUser, $user) {
                $q->where('requester_id', $authUser->id)->where('target_id', $user->id);
            })->orWhere(function ($q) use ($authUser, $user) {
                $q->where('requester_id', $user->id)->where('target_id', $authUser->id);
            })->first();

            // Check if authUser supervises $user
            $isSupervisor = UserRelationship::where('supervisor_id', $authUser->id)
                            ->where('student_id', $user->id)->exists();

            // Check if authUser is student of $user
            $isStudent = UserRelationship::where('student_id', $authUser->id)
                            ->where('supervisor_id', $user->id)->exists();

            // Get the relationship ID if any
            $relationshipId = null;
            if ($isSupervisor) {
                $relationship = UserRelationship::where('supervisor_id', $authUser->id)
                    ->where('student_id', $user->id)->first();
                $relationshipId = $relationship?->id;
            } elseif ($isStudent) {
                $relationship = UserRelationship::where('student_id', $authUser->id)
                    ->where('supervisor_id', $user->id)->first();
                $relationshipId = $relationship?->id;
            }

            $relationshipStatus = [
                'existingRequest' => $existingRequest,
                'isSupervisor' => $isSupervisor,
                'isStudent' => $isStudent,
                'relationshipId' => $relationshipId,
            ];
        } else {
            // If viewing own profile, load incoming requests with requester relation
            $incomingRequests = UserRelationshipRequest::where('target_id', $authUser->id)
                ->where('status', 'pending')
                ->with('requester')
                ->latest()
                ->get();
            $mySupervisors = UserRelationship::where('student_id', $authUser->id)
                ->with('supervisor')->get();

            $myStudents = UserRelationship::where('supervisor_id', $authUser->id)
                ->with('student')->get();
    
        }

        $user->load('freeBadges'); // Load badges and pivot (badge, score)
        $badges = $user->freeBadges->sortByDesc(fn($b) => $b->pivot->score);

        return view("profile.student.profile", compact("user", "badges", "relationshipStatus", "incomingRequests", "mySupervisors", "myStudents"));
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
