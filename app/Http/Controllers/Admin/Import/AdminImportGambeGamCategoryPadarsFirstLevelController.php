<?php
namespace App\Http\Controllers\Admin\Import;

use App\Models\Book;
use App\Models\BookPart;
use App\Models\CategoryBook;
use App\Models\CategoryExam;
use App\Models\CategoryGamBeGam;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Panther\Panther;
use Illuminate\Support\Facades\Storage;
use App\Services\Uploader\StorageManagerService;

class AdminImportGambeGamCategoryPadarsFirstLevelController extends Controller
{

 
   private $xpath;
   private $url;
   private $baseCat;
 
   public function categoryImport()
   {
      DB::statement('SET FOREIGN_KEY_CHECKS=0;');
      CategoryGamBeGam::truncate();
      DB::statement('SET FOREIGN_KEY_CHECKS=1;');
      $this->url = 'https://paadars.com/';
      $this->baseCat  = new CategoryGamBeGam();
      $this->baseCat->name = 'دسته بندی';
      $this->baseCat->url = $this->url;
      $this->baseCat->save();
   
      $this->initialize();
      $this->createSubCats();                              
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
     $items = $this->xpath->query('//div[contains(@class, "pa-level-item")]/a');

      $data = [];

      foreach ($items as $item) {
         $href = $item->getAttribute('href');
         $titleNode = $this->xpath->query('.//div[contains(@class, "pa-level-title")]/p', $item)->item(0);
         $title = $titleNode ? trim($titleNode->textContent) : '';

         $data[] = [
            'title' => $title,
            'url'   => 'https://paadars.com' . $href,
         ];
      }
      // Output result
      foreach ($data as $item) {
         $isCatExist = !CategoryGamBeGam::where('url',  $item['url'])->count();
         if($isCatExist)
         {
            $newCat = new CategoryGamBeGam();
            $newCat->name = $item['title'];
            $newCat->url = $item['url'];
            $newCat->appendToNode($this->baseCat)->save();
         }
      }
   }
   
}

