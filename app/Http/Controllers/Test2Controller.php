<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\User;
use App\Models\Question;
use Illuminate\Http\File;
use App\Mail\UserRegistered;
use App\Models\CategoryExam;
use App\Models\CategoryFree;
use Illuminate\Http\Request;
use App\Models\CategoryJozve;
use App\Models\QuestionsTemp;
use App\Jobs\SendEmailToUsers;
use App\Models\CategoryQuestion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Jobs\Notification\Sms\SendSms;
use Illuminate\Support\Facades\Storage;
use App\Services\Desktop\DesktopService;
use App\Jobs\Notification\Email\SendEmail;
use App\Services\Uploader\StorageManagerService;
use App\Jobs\Notification\Sms\SendSmsToMultipleUser;
use App\Services\Notification\Sms\Contracts\SmsTypes;
use App\Services\Quiz\SubService\SaveQuizDataService;
use App\Services\Desktop\SubService\MyProgressService;
use App\Jobs\Notification\Email\SendEmailWithMailAddress;
use App\Services\CategoryQuestion\CategoriesQuestionService;

class Test2Controller extends Controller
{
    private $saveQuizDataService;   
    private $desktopService;

    public function __construct(SaveQuizDataService $saveQuizDataService, DesktopService $desktopService)
    {
        $this->saveQuizDataService = $saveQuizDataService;
        $this->desktopService = $desktopService;
                     
    }

    public function getCategoriesLevel()
    {
      $user = auth()->user();
      $categories = $user->categoryQuestions;

      $categories = $categories->sortBy('id');
      $lines = [];
      foreach ($categories as $category) {
          $lines[] =$category->id . '$' . $category->path() . '$' . $category->pivot->level;
      }
        $content = implode(PHP_EOL, $lines);
          $fileName = 'categories_level_' . $user->id . '.txt';

          Storage::disk('local')->put($fileName, $content);

        return response()->download(
            storage_path('app/' . $fileName)
        )->deleteFileAfterSend(true);
    }
    



}
