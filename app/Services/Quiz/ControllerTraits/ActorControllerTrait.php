<?php
namespace App\Services\Quiz\ControllerTraits;

use Illuminate\Support\Facades\Auth;


trait ActorControllerTrait
{
    private $user;
    private $role;

    public function setUser()
    {
        return true;
    }

    public function getUser()
    {
        return $this->quizService->getUser();
    }
   
    public function setRole()
    {
        return "Student";
    }

    public function getRole()
    {
        return $this->role;
    }

}