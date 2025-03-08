<?php
namespace App\Services\Desktop;

use Illuminate\Support\Facades\Auth;
use App\Services\Desktop\SubService\MyProgressService;
use App\Services\Desktop\Traits\ActorDesktopServiceTrait;




class DesktopService
{
    use ActorDesktopServiceTrait;
    private $myProgressService;
    public $request;


    public function __construct(MyProgressService $myProgressService)
    {
        $this->myProgressService = $myProgressService;
    }
    public function getProgressData() 
    {
        return $this->myProgressService->getProgressData();
    }

}
