<?php
namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Services\Auth\OTPProfileMobile;
use App\Services\Auth\Traits\hasOTP;
use App\Services\Auth\Traits\hasUsername;
use Illuminate\Http\Request;

class DesktopChangeNameController extends Controller
{

    public function changeNameForm()
    {
        return view('desktop.setting.changeName');
    }

    public function changeName(Request $request)
    {   
        $activeUser =      auth()->user();
        $user = User::where('name', $request->name)->where('id' , "!=" , $activeUser->id)->first();
        if($user)
        {
            return back()->withErrors(['name' => "این نام کاربری قبلا انتخاب شده است"]);

        }

        $activeUser->name = $request->name;
        $activeUser->save();

        return back()->with('success', ' نام کاربری شما با موفقیت تغییر کرد');


    }

}
