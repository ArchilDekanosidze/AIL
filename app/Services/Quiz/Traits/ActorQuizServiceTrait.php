<?php
namespace App\Services\Quiz\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;


trait ActorQuizServiceTrait
{
    private $user;
    private $role;

    public function setUser($user)
    {
        $this->user =User::find($user);
        $this->saveQuizDataService->setUser($this->user);
        $this->updateUserCategorieslevelAndNumberService->setUser($this->user);
        $this->createQuizQuestionService->setUser($this->user);
        $this->createQuizService->setUser($this->user);
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