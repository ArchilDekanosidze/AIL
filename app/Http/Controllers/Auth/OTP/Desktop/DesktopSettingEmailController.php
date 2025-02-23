<?php

namespace App\Http\Controllers\Auth\OTP\Desktop;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Services\Auth\OTPProfileEmail;
use App\Services\Auth\Traits\hasOTP;
use App\Services\Auth\Traits\hasUsername;
use Illuminate\Http\Request;

class DesktopSettingEmailController extends Controller
{

    use hasUsername;
    use hasOTP;
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct(OTPProfileEmail $otp)
    {
        $this->middleware('auth');
        $this->otp = $otp;
    }

    public function add(Request $request)
    {
        if ($this->isOtherUserExists($request->email)) {
            return $this->SendOtherUserExistsEmailResponse();
        }
        if (!$this->isUsernameAnEmail($request->email)) {
            return $this->SendNotValidEmailResponse();
        }
        $request->merge(["username" => $request->email]);
        $response = $this->otp->requestCode();
        return $response == $this->otp::CODE_SENT
        ? $this->SendTokenSuccessResponse()
        : $this->SendTokenFailedResponse();
    }

    protected function SendTokenSuccessResponse()
    {
        return redirect()->route('auth.otp.desktop.setting.email.code')->with('success', __('auth.Code Sent'));
    }

    public function showEnterCodeForm()
    {
        return view('auth.otp.desktop.email-enter-code');
    }

    protected function SendConfirmCodeSuccessResponse()
    {
        return redirect()->route('desktop.setting.setting')->with('success', __('auth.Your email changed succeefully'));
    }

    public function showOTPForm()
    {
        return view('auth.otp.desktop.email');
    }
}
