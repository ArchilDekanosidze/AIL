<?php
namespace App\Http\Controllers\Desktop;



use App\Models\User;
use Illuminate\Http\Request;
use App\Services\Quiz\QuizService;
use App\Http\Controllers\Controller;
use App\Services\Desktop\DesktopService;

class myProgressController extends Controller
{
    private $desktopService;
    private $quizService;
    private $request;

    public function __construct(DesktopService $desktopService,QuizService $quizService, Request $request)
    {
        $this->desktopService = $desktopService;
        $this->request = $request;
        $this->quizService = $quizService;
    }


    public function myProgress(User $user)
    {
        $this->quizService->decayPercentage();
        $authUser = auth()->user();

        $isOwner = $authUser->id === $user->id;

        $isSupervisor = \App\Models\Profile\UserRelationship::where('supervisor_id', $authUser->id)
            ->where('student_id', $user->id)
            ->exists();

        if (! $isOwner && ! $isSupervisor) {
            abort(403, 'شما مجاز به مشاهده پیشرفت این کاربر نیستید.');
        }

        return view('desktop.myProgress.index', [
            'userId' => $user->id,
            'isSupervisor' => $isSupervisor,
        ]);
    }


    public function getChartResult()
    {
        $userId = $this->request->userId;
        $this->desktopService->setUser($userId);
        return $this->desktopService->getProgressData();
    }

}
