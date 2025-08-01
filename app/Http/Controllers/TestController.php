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


    public function updateUserBadge()
    {
      $score = 50;
      $userId = 1;
      $tagId = 23;
      $user = User::find($userId);
      $userBadge = $user->badges->where('id', $tagId)->first();
      if(is_null($userBadge))
      {
        $newScore = $score;
      }
      else
      {
        $newScore = $userBadge->pivot->score + $score;
      }
      $tag = Tag::find($tagId);
      $badgeNames = [
        'bronz1',
        'bronz2',
        'bronz3',
        'silver1',
        'silver2',
        'silver3',
        'gold1',
        'gold2',
        'gold3',
        'platinum1',
        'platinum2',
        'platinum3',
        'dimond1',
        'dimond2',
        'dimond3',
        'legendary1',
        'legendary2',
        'legendary3'
      ];
      $newBadge = null;
      foreach ($badgeNames as $badgeName) {
        if($newScore > $tag->{$badgeName})
        {
          $newBadge = $badgeName;
        }
      }
      $user->badges()->syncWithoutDetaching([$tagId => ['score' => $newScore, 'badge' => $newBadge]]);
    }

    public function upload1()
    {
      $storageManager = new StorageManagerService();
      $content = json_encode(['progress' => '50%', 'last_category' => 12]);

      // Save content to a temporary file
      $tempPath = tempnam(sys_get_temp_dir(), 'upload');
      file_put_contents($tempPath, $content);
  

      $writtenContent = file_get_contents($tempPath);
      if ($writtenContent === $content) {
          dump("Content successfully written to the temporary file.");
      } else {
          dump("Content was not written correctly.");
      }


      // Use File (not UploadedFile)
      $file = new File($tempPath);

      $storageManager->putFileAsPrivate('user_123_progress.json', $file, 'questions');
        if (Storage::disk('private')->exists('questions/user_123_progress.json')) {
          dump("File successfully saved.");
      } else {
          dump("File was not saved.");
      }

      $content = Storage::disk('questions')->get('user_123_progress.json');
      dd($content);



    }

    public function upload2()
    {
      $question = \App\Models\Question::find(1);
      // dd($question);
      if ($question) {
          $front = $question->front;
          echo $front;
      } else {
          echo "Question not found.";
      }
    }


    public function upload(Request $request)
  {
      if ($request->hasFile('upload')) {
          $file = $request->file('upload');
          $filename = time() . '_' . $file->getClientOriginalName();
          $path = $file->storeAs('images', $filename, 'public');
          $url = Storage::url($path);
          return response()->json([
              'uploaded' => 1,
              'fileName' => $filename,
              'url' => $url
          ]);
      }

      return response()->json(['uploaded' => 0]);
  }

  public function createJozveCategory()
  {
    // CategoryJozve::truncate();
    $firstLevels = CategoryExam::where('parent_id', 1)->get();
    $jozveCat = new CategoryJozve();
    $jozveCat->name = 'دسته بندی';
    $jozveCat->save();
    foreach ($firstLevels as $firstLevel) {
      tap(CategoryJozve::create(['name' => $firstLevel->name]), fn($node) => $node->appendToNode($jozveCat)->save());
    }
    foreach ($firstLevels as $firstLevel) {      
          $secondLeves = CategoryExam::where('parent_id', $firstLevel->id)->get();
          $mainCat = CategoryJozve::where('name', $firstLevel->name)->first();
          foreach ($secondLeves as $secondLeve) {
            tap(CategoryJozve::create(['name' => $secondLeve->name]), fn($node) => $node->appendToNode($mainCat)->save());
          }
    }
  }

  public function createFreeCategory()
  {
    // CategoryFree::truncate();
    $firstLevels = CategoryExam::where('parent_id', 1)->get();
    $freeCat = new CategoryFree();
    $freeCat->name = 'دسته بندی';
    $freeCat->save();
    foreach ($firstLevels as $firstLevel) {
      tap(CategoryFree::create(['name' => $firstLevel->name]), fn($node) => $node->appendToNode($freeCat)->save());
    }
    foreach ($firstLevels as $firstLevel) {      
          $secondLeves = CategoryExam::where('parent_id', $firstLevel->id)->get();
          $mainCat = CategoryFree::where('name', $firstLevel->name)->first();
          foreach ($secondLeves as $secondLeve) {
            tap(CategoryFree::create(['name' => $secondLeve->name]), fn($node) => $node->appendToNode($mainCat)->save());
          }
    }
  }

  public function dinvazendegi() {
    $tempQuestions = QuestionsTemp::all();
    $tempQuestionsCatIds = $tempQuestions->pluck('category_question_id')->unique();
    dd($tempQuestionsCatIds);
    foreach ($tempQuestionsCatIds as $id) {
      
    }
  }

  public function removeQuestionsFromCatAndSetQuestionTableIdForIncreament()
  {
    $questions = CategoryQuestion::find(3)->allQuestion()->each->delete();
    // foreach ($questions as $question) {
    //   $question->delete();
    // }
    dd($questions);
  }

}
