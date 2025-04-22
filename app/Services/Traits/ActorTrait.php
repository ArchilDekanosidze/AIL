<?php
namespace App\Services\Traits;

use Illuminate\Support\Facades\Auth;


trait ActorTrait
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
        if($this->user)
        {
            return $this->user;
        }
        else
        {
            return auth()->user();
        }
    }
   
    public function setRole()
    {
        return "Student";
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }
    public function getRequest()
    {
        return $this->request;
    }

}