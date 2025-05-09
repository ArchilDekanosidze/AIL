<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use App\Services\Auth\Traits\AuthBackend\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
     */

    use SendsPasswordResetEmails;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showForgetForm()
    {
        return view('auth.forget-password');
    }

    public function sendResetLink(Request $request)
    {
        $this->validateForm($request);
        $resposne = Password::broker()->sendResetLink($request->only('email'));
        if ($resposne == Password::RESET_LINK_SENT) {
            return back()->with('success', __('auth.resetLinkSent'));
        }
        return back()->withErrors(['resetLinkFailed', __('auth.reset Link Failed')]);
    }
    protected function validateForm($request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users'],
        ]);
    }
}
