<?php
namespace App\Services\Traits;

use Illuminate\Support\Facades\Auth;


trait ActorControllerTrait
{
    private $user;
    private $role;
    private $request;


    public function setUser($user)
    {
        $this->user = $user;
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