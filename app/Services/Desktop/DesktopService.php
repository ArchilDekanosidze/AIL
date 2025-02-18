<?php
namespace App\Services\Desktop;

use App\Services\Desktop\MyProgressService;



class DesktopService
{
    private $myProgressService;

    public function __construct(MyProgressService $myProgressService)
    {
        $this->myProgressService = $myProgressService;
    }
    public function getProgressData()
    {
        return $this->myProgressService->getProgressData();
    }

}
