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
    private $request;

    public function __construct(DesktopService $desktopService, Request $request)
    {
        $this->desktopService = $desktopService;
        $this->request = $request;

    }


    public function myProgress(User $user)
    {
        $userId = $user->id;
        return view('desktop.myProgress.index', compact("userId"));
    }

    public function getChartResult()
    {
        $userId = $this->request->userId;
        $this->desktopService->setUser($userId);
        return $this->desktopService->getProgressData();
    }

}
