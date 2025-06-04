<?php
namespace App\Http\Controllers\Admin\Import;

use App\Models\Book;
use App\Models\BookPart;
use App\Models\GamBeGam;
use App\Models\CategoryBook;
use App\Models\CategoryExam;
use App\Models\CategoryGamBeGam;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Panther\Panther;
use Illuminate\Support\Facades\Storage;
use App\Services\Uploader\StorageManagerService;

class AdminImportGambeGamCategoryPadarsForthLevelController extends Controller
{

 
   private $xpath;
   private $url;
   private $baseCat;
 
   public function categoryImport()
   {


      $cats = CategoryGamBeGam::where('parent_id', 1)->get();
      $cats = CategoryGamBeGam::whereIn('parent_id', $cats->pluck('id'))->get();
      $cats = CategoryGamBeGam::whereIn('parent_id', $cats->pluck('id'))->get();
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
      
      $items = $this->xpath->query('//div[contains(@class, "book-info-item-top")]');

      $data = [];

      foreach ($items as $item) {
         // Get the image URL
         $imgNode = $this->xpath->query('.//img', $item)->item(0);
         $imgUrl = $imgNode ? $imgNode->getAttribute('src') : '';

         // Get the title from the <h2>
         $titleNode = $this->xpath->query('.//h2', $item)->item(0);
         $title = $titleNode ? trim($titleNode->textContent) : '';

         $data[] = [
            'title' => $title,
            'image' => $imgUrl,
         ];
      }
      foreach ($data as $item) {
         $isGamBeGamExist  = !GamBeGam::where('url',  $item['image'])->count();
         if($isGamBeGamExist)
         {
            $newGhambeGhame = new GamBeGam();
            $newGhambeGhame->title = $item['title'];
            $newGhambeGhame->url = $item['image'];
            $newGhambeGhame->category_gam_be_gam_id = $parentCat->id;
            $newGhambeGhame->save();
         }
      } 
   }
   
}

