<?php
namespace App\Services\Desktop\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;


trait ActorDesktopServiceTrait
{
    private $user;
    private $role;

    public function setUser($user)
    {
        $this->user =User::find($user);
        $this->myProgressService->setUser($this->user);
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

}