<?php
namespace App\Http\Controllers\Admin\Import;

use App\Models\Book;
use App\Models\Exam;
use App\Models\ExamTag;
use App\Models\BookPart;
use App\Models\CategoryBook;
use App\Models\CategoryExam;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Panther\Panther;
use Illuminate\Support\Facades\Storage;
use App\Services\Uploader\StorageManagerService;

class AdminImportKanoonSoalController extends Controller
{

 
   private $xpath;
   private $cat;
   public function soalImport()
   {

      $baseCats = CategoryExam::where('parent_id', 1)->get();
      $cats = CategoryExam::whereIn('parent_id', $baseCats->pluck('id'))->where('id' , '>', 100)->get();
      //  dd($cats);          
      foreach ($cats as $cat) 
      {         
         dump($cat->id);
         $this->cat = $cat;
         $this->initialize();
         $this->addExams();         
      }                     
   }   
   
   public function saveHtml()
   {
      $baseCats = CategoryExam::where('parent_id', 1)->get();
      $cats = CategoryExam::whereIn('parent_id', $baseCats->pluck('id'))->where('id' , '>', 98)->get();
      foreach ($cats as $cat) 
      {         
         dump($cat->id);
         $this->cat = $cat;
         $response = Http::get($this->cat->url);
         $html = $response->body();

         // Build file path beside controller
         $folder = __DIR__ . '/kanoonSoal';
         if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
         }

         $filePath = $folder . '/' . $this->cat->id . '.html';

         // Save HTML content to file
         file_put_contents($filePath, $html);       
         }   
   }


   public function initialize()
   {
      // dd($this->cat->url);
      $filePath = __DIR__ . "/kanoonSoal/{$this->cat->id}.html";
      if (!file_exists($filePath)) {
      }
      $html = file_get_contents($filePath);
      // dd($html);

      $dom = new \DOMDocument();
      @$dom->loadHTML($html);
      $this->xpath = new \DOMXPath($dom);
   }

   public function addExams()
   {
      $lessonBlocks = $this->xpath->query('//div[contains(@class, "LessonExams")]');
   
      foreach ($lessonBlocks as $lessonBlock) {
         // Get Lesson Type
         $lessonTypeDiv = (new \DOMXPath($lessonBlock->ownerDocument))
            ->query('.//div[contains(@class, "LessonType")]', $lessonBlock)
            ->item(0);
         $lessonType = trim($lessonTypeDiv?->textContent);

         
         $newCategory = CategoryExam::create(['name' => $lessonType]);
         $newCategory->appendToNode($this->cat)->save();
         

         // Process each exam
         $examFiles = (new \DOMXPath($lessonBlock->ownerDocument))
            ->query('.//div[contains(@class, "examfile")]', $lessonBlock);

         foreach ($examFiles as $examDiv) {            
            $a = (new \DOMXPath($lessonBlock->ownerDocument))->query('.//a', $examDiv)->item(0);
            $href = $a?->getAttribute('href');
            $url ='https://www.kanoon.ir' . $href;

            
            $divs = $a->getElementsByTagName('div');
            $firstDivText = '';
            if ($divs->length > 0) {
               $title = trim($divs->item(0)->textContent);
            }

            $hasAnswer = $examDiv->getAttribute('answer') == '1' ? 1 : 0;
            $state = $examDiv->getAttribute('state');
            $city = $examDiv->getAttribute('city');
            $school = $examDiv->getAttribute('school');

            $exam = Exam::create([
                  'category_exam_id' => $newCategory->id,
                  'title' => $title,
                  'url' => $url,
                  'has_answer' => $hasAnswer,
                  'state' => $state,
                  'city' => $city,
                  'school_type' => $school,
            ]);

            $spans = (new \DOMXPath($lessonBlock->ownerDocument))->query('.//span', $examDiv);
            foreach ($spans as $span) {
                  $tagText = trim($span->textContent);
                  if (str_contains($tagText, 'دانلود')) continue;
                  $tag = ExamTag::create(['name' => $tagText, 'exam_id' => $exam->id]);
            }
         }
      }
   }


   
}

