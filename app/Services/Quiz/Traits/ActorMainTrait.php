<?php
namespace App\Services\Quiz\Traits;

use Illuminate\Support\Facades\Auth;


trait ActorMainTrait
{
    private $user;
    private $role;

    public function setUser($user)
    {
        $this->user = $user;
        $this->saveQuizDataService->setUser($user);
        $this->updateUserCategorieslevelAndNumberService->setUser($user);
        $this->createQuizQuestionService->setUser($user);
        $this->createQuizService->setUser($user);
    }

    public function getUser()
    {
        return $this->user;
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