<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function RredirectToProvider($driver)
    {
        return Socialite::driver($driver)->redirect();
    }

    public function providerCallback($driver)
    {
        $user = Socialite::driver($driver)->user();

        Auth::login($this->findOrCreateUser($user, $driver));

        return redirect()->intended();
    }

    protected function findOrCreateUser($userInfo, $driver)
    {
        $providerUser = User::where([
            'email' => $userInfo['email'],
        ])->first();

        if (!is_null($providerUser)) {
            return $providerUser;
        }

        $user = User::create([
            'email' => $userInfo['email'],
            'name' => $userInfo['name'],
        ]);
        $user->email_verified_at = now();
        $user->save();
        return $user;
    }
}
