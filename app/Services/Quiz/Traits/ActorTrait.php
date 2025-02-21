<?php
namespace App\Services\Quiz\Traits;

use Illuminate\Support\Facades\Auth;


trait ActorTrait
{
    private $user;
    private $role;

    public function setUser($user)
    {
        $this->user = $user;
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