<?php

namespace App\Http\Controllers\Auth\OTP\Desktop;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Services\Auth\OTPProfileMobile;
use App\Services\Auth\Traits\hasOTP;
use App\Services\Auth\Traits\hasUsername;
use Illuminate\Http\Request;

class DesktopSettingMobileController extends Controller
{

    use hasUsername;
    use hasOTP;
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct(OTPProfileMobile $otp)
    {
        $this->middleware('auth');
        $this->otp = $otp;
    }

    public function add(Request $request)
    {
        if ($this->isOtherUserExists($request->mobile)) {
            return $this->SendOtherUserExistsMobileResponse();
        }

        if (!$this->isUsernameAnMobile($request->mobile)) {
            return $this->SendNotValidMobileNumberResponse();
        }
        $request->merge(["username" => $request->mobile]);
        $response = $this->otp->requestCode();
        return $response == $this->otp::CODE_SENT
        ? $this->SendTokenSuccessResponse()
        : $this->SendTokenFailedResponse();
    }

    protected function SendTokenSuccessResponse()
    {
        return redirect()->route('auth.otp.desktop.setting.mobile.code')->with('success', __('auth.Code Sent'));
    }

    public function showEnterCodeForm()
    {
        return view('auth.otp.desktop.mobile-enter-code');
    }

    protected function SendConfirmCodeSuccessResponse()
    {
        return redirect()->route('desktop.setting.setting')->with('success', __('auth.Your mobile number changed succeefully'));
    }

    public function showOTPForm()
    {
        return view('auth.otp.desktop.mobile');
    }
}
