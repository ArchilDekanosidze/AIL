<?php
namespace App\Http\Controllers\Admin\Import;

use App\Models\Book;
use App\Models\BookPart;
use App\Http\Controllers\Controller;
use App\Models\CategoryBook;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Panther\Panther;
use Illuminate\Support\Facades\Storage;
use App\Services\Uploader\StorageManagerService;

class AdminImportBookController extends Controller
{

   private $book;
   private $xpath;
   private $chap_url;
   private $urlId;
   private $bookParts;
   private $allLineages;
   private $title;

   public function import(StorageManagerService $storageManagerService)
   {
        // $lastBook = Book::orderBy('id', 'desc')->first();
        // $lastUrl = $lastBook->chap_url;
        // $lastUrlId = basename(parse_url($lastUrl, PHP_URL_PATH)); 
        // $lastUrlId = $lastUrlId +1;
        $lastUrlId = 1;
        for ($i=$lastUrlId; $i < 14000; $i++) { 
            dump($i);
            $this->urlId = $i;
            $this->initialize();        
            $this->getTitle();
            if($this->title != "صفحه مورد نظر یافت نشد.")
            {
                $this->getYear();
                $this->getCode();
                $this->getImage();
                $this->getBookParts();
                $this->book->save();
                $this->saveBookParts();
                $this->getDooreh();
                $this->saveDooreh();
            }
        }
   }    


   public function initialize()
   {
        $this->chap_url = 'http://chap.sch.ir/books/' . $this->urlId;
        $response = Http::retry(300, 5000)->timeout(0)->get($this->chap_url);
        $this->book = new Book();
        $this->book->chap_url = $this->chap_url;
        $dom = new \DOMDocument();
        @$dom->loadHTML($response->body()); 
        $this->xpath = new \DOMXPath($dom);
   }


   public function getTitle()
   {
        $titleNode = $this->xpath->query('//h1[@class="page__title title" and @id="page-title"]')->item(0);

        if ($titleNode) {
            $text = trim($titleNode->nodeValue);
            $this->book->title = $text;
            $this->title = $text;
        }
   }


   
   public function getYear()
   {
        $yearNode = $this->xpath->query('//div[contains(@class, "field-name-field-year")]//div[@class="field-item even"]')->item(0);
        if ($yearNode) {
            $year = trim($yearNode->nodeValue);
            $this->book->year = $year;
        }
   }

   public function getCode()
   {
        $elements = $this->xpath->query('//div[@class="field-label" and contains(text(), "کد کتاب")]');
        if ($elements->length > 0) {
            $bookCodeElement = $elements->item(0)->nextSibling;            
            $bookCode = trim($bookCodeElement->nodeValue);            
            $this->book->code = $bookCode;
        }
   }

   public function getImage()
   {
        $imageElements = $this->xpath->query('.//img'); // Search for all <img> tags
            
        // $imageElements = $xpath->query('.//div[@class="field-name-field-book-image"]//img');
        //  dd($imageElements->length);
        if ($imageElements->length > 0) {
            foreach ($imageElements as $imageElement) {
                $imageUrl = $imageElement->getAttribute('src');
                if (strpos($imageUrl, 'http://chap.sch.ir/sites/default/files') === 0) {                
                    // $imageContent = file_get_contents($imageUrl);
                    // $imageName = basename($imageUrl); // Extract the image file name from URL
                    // $idStr = str_pad($urlId, 6, '0', STR_PAD_LEFT);
                    // $pathParts = str_split($idStr);
                    // $folderPath = implode('/', $pathParts);
                    // $imagePath = $folderPath . '/' . $imageName;
                    // $disk =Storage::disk('bookImages') ;                                  
                    // $filePath = $imagePath . $imageName;            
                    // $disk->put($filePath,$imageContent);
                    $this->book->image = $imageUrl;
                }
            }          
        }
   }
   
   public function getBookParts()
   {
        $this->bookParts = [];
        $entries_file = $this->xpath->query('//div[contains(@class, "field-name-field-book-file")]//a');
        foreach ($entries_file as $entry) {
            
            $name = $entry->textContent;
            $url = $entry->getAttribute('href');
            // dd($entry->parentNode->parentNode->parentNode->getElementsByTagName('td')[0]);
            if($entry->parentNode->parentNode->parentNode->getElementsByTagName('td')[1] != null)
            {
                $size = $entry->parentNode->parentNode->parentNode->getElementsByTagName('td')[1]->textContent; // Get size from adjacent td
                
                if (strpos($url, 'http://chap.sch.ir/sites/default/files/lbooks/') === 0) {
                    $this->book->fileName = $name;
                    $this->book->fileUrl = $url;
                    $this->book->fileSize = $size;
                } elseif (strpos($url, 'http://chap.sch.ir/sites/default/files/books/') === 0) {
                $part = ['name' => $name, 'url' => $url, 'size' => $size];
                $this->bookParts [] = $part;
                }
            }            
        }
   }

   public function saveBookParts()
   {
        foreach ($this->bookParts as $part) {
            // Save each book part
            $bookPart = new BookPart();
            $bookPart->name = $part['name'];
            $bookPart->url = $part['url'];
            $bookPart->size = $part['size'];
            $bookPart->book_id = $this->book->id; // Associate with the current book
            $bookPart->save();
        }
   }

   public function getDooreh()
   {
        $this->allLineages=[];
        $dorehLabel = $this->xpath->query('//div[@class="field-label" and contains(text(), "دوره تحصیلی")]');
        if ($dorehLabel->length > 0) {
            $dorehNode = $dorehLabel->item(0);    
            $dorehItems = [];
            $current = $dorehNode->nextSibling;
            // Loop through the next siblings
            while ($current && !($current->nodeType === XML_ELEMENT_NODE && $current->getAttribute('class') === 'field-items')) {
                $current = $current->nextSibling;
            }
            if ($current) {
                // Now select all field-item inside field-items
                $fieldItems = $this->xpath->query('.//div[contains(concat(" ", normalize-space(@class), " "), " field-item ")]', $current);
                $values = [];
                foreach ($fieldItems as $fieldItem) {
                    $lineageItems = $this->xpath->query('.//span[contains(concat(" ", normalize-space(@class), " "), " lineage-item ")]', $fieldItem);
                    // dd($lineageItems->length, $fieldItem->ownerDocument->saveHTML($fieldItem));
                    $lineageTexts = [];
                    foreach ($lineageItems as $lineage) {
                        // dd($lineageItems->ownerDocument->saveHTML($lineageItems));

                        $lineageTexts[] = trim($lineage->textContent);
            
                    }
                    $this->allLineages[] = $lineageTexts;
                }
            } 
        }
   }

   public function saveDooreh()
   {
        $categoryIds = []; // Collect all $baseCat->id here
        foreach ($this->allLineages as $lineage) {
            $baseCat = CategoryBook::where('name', $lineage[0])->where('parent_id', 1)->first();
            for ($i=1; $i < count($lineage); $i++) { 
                $subCat = CategoryBook::where('name', $lineage[$i])->where('parent_id', $baseCat->id)->first();
                $baseCat = $subCat;
            }  
            $categoryIds[] = $baseCat->id;         
        }

        $this->book->categories()->syncWithoutDetaching($categoryIds);
   }
   
}

