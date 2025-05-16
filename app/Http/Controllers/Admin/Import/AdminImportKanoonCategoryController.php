<?php
namespace App\Http\Controllers\Admin\Import;

use App\Models\Book;
use App\Models\BookPart;
use App\Models\CategoryBook;
use App\Models\CategoryExam;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Panther\Panther;
use Illuminate\Support\Facades\Storage;
use App\Services\Uploader\StorageManagerService;

class AdminImportKanoonCategoryController extends Controller
{

 
   private $xpath;
   private $url;
 
   public function categoryImport()
   {

      $cats = CategoryExam::where('parent_id', 1)->get();
      if(count($cats) == 0)
      {
         $urls = ['https://www.kanoon.ir/Public/ExamQuestions'];
      }
      else
      {
         $urls = $cats->pluck('url');
      }
      foreach ($urls as $url) 
      {
         $this->url = $url;
         $this->initialize();
         $this->createSubCats();         
      }
              
       
   }    


   public function initialize()
   {
        $response = Http::get($this->url);
        $dom = new \DOMDocument();
        @$dom->loadHTML($response->body()); 
        $this->xpath = new \DOMXPath($dom);
   }

   public function createSubCats()
   {
      $nodes = $this->xpath->query('//ol[contains(@class, "breadcrumb")]/li');

      $breadcrumbItems = [];
      foreach ($nodes as $li) {
         $span = $this->xpath->query('.//span[@itemprop="name"]', $li)->item(0);
         $meta = $this->xpath->query('.//meta[@itemprop="position"]', $li)->item(0);
         $aTag = $this->xpath->query('.//a', $li)->item(0);

         $breadcrumbItems[] = [
               'name' => trim($span->textContent ?? ''),
               'url' => $aTag?->getAttribute('href'),
               'position' => (int) ($meta?->getAttribute('content') ?? 0),
         ];
      }
      if($breadcrumbItems == [])
      {
         $mainCategory = CategoryExam::firstOrCreate(['name' => 'نمونه سوال امتحانی'])->first();
      }
      else
      {
         $baseCat =  CategoryExam::where(['name' => $breadcrumbItems[0]])->first();
         for ($i=1; $i <count($breadcrumbItems) ; $i++) { 
            $subCat =  CategoryExam::where(['name' => $breadcrumbItems[$i]])->where('parent_id', $baseCat->id)->first();
            $baseCat = $subCat;         
         }               
         $mainCategory = $baseCat;                  
      }


      $nodes = $this->xpath->query('//a[contains(@class, "list-group-item")]');

      foreach ($nodes as $node) {
         // Case 1: Try to find the LessonName span if it exists
         $lessonSpan = $this->xpath->query('.//span[contains(@class, "LessonName")]', $node)->item(0);

         if ($lessonSpan) {
            // Extract from LessonName span (ignores badge)
            $title = trim($lessonSpan->textContent);
         } else {
            // Case 2: Fallback — no span, just plain text (ignores icon)
            $title = trim(preg_replace('/\s+/', ' ', $node->textContent));
         }

         // Clean out any badge counts or extra whitespace
         $title = preg_replace('/\d+\s*نمونه سوال.*/u', '', $title); // removes "813 نمونه سوال" etc.
         $title = trim($title);

         $url ='https://www.kanoon.ir'  . $node->getAttribute('href'); // e.g., /Public/ExamQuestions?group=1&lesson=203

         // Get group ID from href
         $href = $node->getAttribute('href');
         preg_match('/group=(\d+)/', $href, $matches);
         $groupId = $matches[1] ?? null;

         if ($groupId && $title) {
            $newCategory = CategoryExam::create(['name' => $title, 'url' => $url]);
            $newCategory->appendToNode($mainCategory)->save();
         }
      }
   }



   
}

