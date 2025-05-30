<?php

namespace App\Http\Controllers\Auth;

use App\Rules\PasswordRule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Password;
use App\Services\Auth\Traits\AuthBackend\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
     */
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showResetForm(Request $request)
    {
        return view('auth.reset-password', [
            'email' => $request->query('email'),
            'token' => $request->query('token'),
        ]);
    }

    public function reset(Request $request)
    {
        $this->validateForm($request);
        $response = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $this->resetPassword($user, $password);
                $user->markEmailAsVerified();
            }
        );
        if ($response == Password::PASSWORD_RESET) {
            return redirect()->route('auth.login')->with('success', __('auth.passwordChanged'));
        } else {
            return back()->withErrors(['cantChangePassword' => __('auth.cant Change Password')]);
        }
    }
    protected function validateForm($request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users'],
            'password' => ['confirmed', new PasswordRule()],
            'token' => ['required', 'string'],
        ]);
    }

    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->save();
    }
}
