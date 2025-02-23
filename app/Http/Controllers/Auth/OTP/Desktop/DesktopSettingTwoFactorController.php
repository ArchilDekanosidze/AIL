<?php

namespace App\Http\Controllers\Auth\OTP\Desktop;

use App\Http\Controllers\Controller;
use App\Services\Auth\OTPProfileTwoFactor;
use App\Services\Auth\Traits\hasOTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DesktopSettingTwoFactorController extends Controller
{
    use hasOTP;

    protected $otp;

    public function __construct(OTPProfileTwoFactor $otp)
    {
        $this->middleware('auth');
        $this->otp = $otp;
    }

    public function showToggleForm()
    {
        return view('auth.otp.desktop.two-factor.toggle');
    }

    public function sendTokenForEmail(Request $request)
    {
        $user = Auth::user();
        $request->merge(["username" => $user->email]);
        return $this->sendToken();
    }

    public function sendTokenForMobile(Request $request)
    {
        $user = Auth::user();
        $request->merge(["username" => $user->mobile]);
        return $this->sendToken();
    }

    public function sendToken()
    {
        $response = $this->otp->requestCode();
        return $response == $this->otp::CODE_SENT
            ? $this->SendTokenSuccessResponse()
            : $this->SendTokenFailedResponse();
    }

    protected function SendTokenSuccessResponse()
    {
        return redirect()->route('auth.otp.desktop.setting.two.factor.code.form')->with('success', __('auth.Code Sent'));
    }

    public function showEnterCodeForm()
    {
        return view('auth.otp.desktop.two-factor.enter-code');
    }

    protected function validateForm(Request $request)
    {
        return true;
    }

    public function deactivate()
    {
        $this->otp->deactivate(Auth::user());
        return back()->with('success', 'احراز هویت دو مرحله ای غیر فعال شد');
    }

    protected function SendConfirmCodeSuccessResponse()
    {
        session()->regenerate();
        return redirect()->route('auth.otp.desktop.setting.two.factor.toggle.form')->with('success', __('auth.Two Factor Activated'));
    }
}
