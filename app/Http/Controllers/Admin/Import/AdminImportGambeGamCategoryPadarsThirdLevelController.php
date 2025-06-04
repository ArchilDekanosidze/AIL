<?php
namespace App\Http\Controllers\Admin\Import;

use App\Models\Book;
use App\Models\BookPart;
use App\Models\CategoryBook;
use App\Models\CategoryExam;
use App\Http\Controllers\Controller;
use App\Models\CategoryGamBeGam;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Panther\Panther;
use Illuminate\Support\Facades\Storage;
use App\Services\Uploader\StorageManagerService;

class AdminImportGambeGamCategoryPadarsThirdLevelController extends Controller
{

 
   private $xpath;
   private $url;
   private $baseCat;
 
   public function categoryImport()
   {
      $cats = CategoryGamBeGam::where('parent_id', 1)->get();
      $cats = CategoryGamBeGam::whereIn('parent_id', $cats->pluck('id'))->get();
      foreach ($cats as $cat) 
      {      
         $this->url = $cat->url . 'exercises/';
         $this->initialize();
         $this->createSubCats($cat);         
      }
              
       
   }    


   public function initialize()
   {
        $response = Http::get($this->url);
        $dom = new \DOMDocument();
        @$dom->loadHTML($response->body()); 
        $this->xpath = new \DOMXPath($dom);
   }

   public function createSubCats($parentCat)
   {
      
      $elements = $this->xpath->query('//div[contains(@class, "pa-element-title")]');

      foreach ($elements as $element) {
         $linkNode = $this->xpath->query('.//h3/a', $element)->item(0);
         $descNode = $this->xpath->query('.//p', $element)->item(0);

         $href = $linkNode ? $linkNode->getAttribute('href') : '';
         $title = $linkNode ? trim($linkNode->textContent) : '';
         $desc = $descNode ? trim($descNode->textContent) : '';
         $fullTitle = $desc ? $title . ' - ' . $desc : $title;

         $data[] = [
            'title' => $fullTitle,
            'url' => 'https://paadars.com' . $href,
         ];
      }
      if(!isset($data))
      {
         return;
      }
      foreach ($data as $item) {
         $isCatExist = !CategoryGamBeGam::where('url',  $item['url'])->count();
         if($isCatExist)
         {
            $newCat = new CategoryGamBeGam();
            $newCat->name = $item['title'];
            $newCat->url = $item['url'];
            $newCat->appendToNode($parentCat)->save();
         }
      } 

   }
   
}

