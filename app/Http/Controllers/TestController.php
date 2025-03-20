<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Question;
use App\Mail\UserRegistered;
use Illuminate\Http\Request;
use App\Models\QuestionsTemp;
use App\Jobs\SendEmailToUsers;
use App\Models\CategoryQuestion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Jobs\Notification\Sms\SendSms;
use App\Services\Desktop\DesktopService;
use App\Jobs\Notification\Email\SendEmail;
use App\Jobs\Notification\Sms\SendSmsToMultipleUser;
use App\Services\Notification\Sms\Contracts\SmsTypes;
use App\Services\Quiz\SubService\SaveQuizDataService;
use App\Services\Desktop\SubService\MyProgressService;
use App\Jobs\Notification\Email\SendEmailWithMailAddress;
use App\Services\CategoryQuestion\CategoriesQuestionService;

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

    public function addCatToUserMinChange(CategoriesQuestionService $categoriesQuestionService)
    {
      $categoriesQuestionService->addCategoryToUser();
    }

    public function catQuestionsCount(Request $request)
    {
      $parentCategory = CategoryQuestion::find($request->id);
      dd(Question::count());
      dd(count($parentCategory->allQuestion()));

    }

    public function remoteDB()
    {
      $user = User::find(1);
      $model = new User();

      $model->setConnection('remoteConnection');
      $model->name = "ali";
      $model->save();
    }

    public function myProgress(Request $request,MyProgressService $myProgressService)
    {
      $request->merge(["datePickerFrom"=>"1403/11/27"]);
      $request->merge(["datePickerTo"=>"1403/12/24"]);
      $request->merge(["spanTimeSelect"=>"hour"]);
      $request->merge(["parentCategoryId"=>"1"]);

      
      $myProgressService->setRequest($request);
      $data = $myProgressService->getProgressData();
      dd($data);
    }

    public function loginAs($id)
    {
      Auth::guard('web')->loginUsingId($id, true);
      request()->session()->regenerate();
      return redirect()->route('desktop.student.index');
    }

    public function findTheListId()
    {
      $catId = 2161;
      $questionsId = CategoryQuestion::find($catId)->allQuestion()->pluck('id')->toArray();
      dd(min($questionsId));
    }

    public function removeDuplicatedQuestions()
    {
      $catId = 3020;
      $questionsId = CategoryQuestion::find($catId)->allQuestion()->pluck('id');
      Question::destroy($questionsId);
    }

    public function transferImages() {
       $sourceFolder = public_path('images');
       $desTinationFolder = public_path('temp');
       $files = File::allFiles($sourceFolder);
       foreach ($files as $file) {
          $destination = $desTinationFolder . '/' . $file->getFilename();
          File::move($file->getRealPath(), $destination);
       } 

    }



}
