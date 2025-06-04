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

class AdminImportGambeGamCategoryPadarsSecondLevelController extends Controller
{

 
   private $xpath;
   private $url;
   private $baseCat;
 
   public function categoryImport()
   {


      $cats = CategoryGamBeGam::where('parent_id', 1)->get();
      $urls = $cats->pluck('url');
      foreach ($cats as $cat) 
      {
         $this->url = $cat->url;
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
      $bookItems = $this->xpath->query('//div[contains(@class, "pa-book-item")]/a');
      $data = [];

      foreach ($bookItems as $item) {
         $href = $item->getAttribute('href');
         $titleNode = $this->xpath->query('.//div[contains(@class, "pa-book-item-title")]/h3', $item)->item(0);
         $title = $titleNode ? trim($titleNode->textContent) : '';
         $subItems[] = ['title' => $title, 'url' => 'https://paadars.com' . $href];
      }

      foreach ($subItems as $item) {
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

