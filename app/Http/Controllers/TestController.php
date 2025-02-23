<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\UserRegistered;
use App\Jobs\SendEmailToUsers;
use Illuminate\Support\Facades\Auth;
use App\Jobs\Notification\Sms\SendSms;
use App\Services\Desktop\DesktopService;
use App\Jobs\Notification\Email\SendEmail;
use App\Jobs\Notification\Sms\SendSmsToMultipleUser;
use App\Services\Notification\Sms\Contracts\SmsTypes;
use App\Services\Quiz\SubService\SaveQuizDataService;
use App\Jobs\Notification\Email\SendEmailWithMailAddress;

class TestController extends Controller
{
    private $saveQuizDataService;
    private $desktopService;

    public function __construct(SaveQuizDataService $saveQuizDataService, DesktopService $desktopService)
    {
        $this->saveQuizDataService = $saveQuizDataService;
        $this->desktopService = $desktopService;

    }
    
    public function index()
    {
      // dd(now()->timestamp);
      // $quiz = Quiz::find(110);
      // $this->saveQuizDataService->saveQuizData($quiz);
      $user = Auth::loginUsingId(1);
      $this->desktopService->setUser($user);
      $this->desktopService->getProgressData();

    }

    public function emailTest() {
      $mailable = new UserRegistered();
      $user = User::find(1);
      SendEmail::dispatch($user, $mailable);
      SendEmailWithMailAddress::dispatch(['h.mirshekar69@gmail.com', 'dekanosidzearchil@gmail.com'], $mailable);
    }

    public function smsTest() {
      $user = User::find(1);
      $data = [
        'type' => SmsTypes::OTP_CODE,
        'variables' => ['verificationCode' => 123],
      ];
      SendSms::dispatch($user, $data);
    }

    public function logout()
    {
      Auth::logout();
    }
 
}
