<?php
namespace App\Http\Controllers\Admin\Import;

use DOMXPath;
use DOMDocument;
use App\Models\Question;
use App\Models\QuestionsTemp;
use App\Models\CategoryQuestion;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Services\CategoryQuestion\CategoriesQuestionService;

class AdminImportController extends Controller
{
    private $doc;
    private $xpath;
    private $allData = [];
    private $QuestionText = null;
    private $AnswerText = null;
    private $Choice1 = null;
    private $Choice2 = null;
    private $Choice3 = null;
    private $Choice4 = null;
    private $type;
    private $level;
    private $category_question_id;
    private $correctAnswer;
    private $payeId = "5";
    private  $folderPath ;
    private $questionId  ;

    private $qIds = [];


    public function import()
    {       
        //copy(document.querySelector(.firefox).outerHTML)
        
        $rawDirectoryPath = __DIR__ . '/texts';
        $filesName = array_diff(scandir($rawDirectoryPath), ['.', '..']);
        $filesName = array_slice($filesName, 0, 3);
        // dd($filesName);
        foreach ($filesName as $fileName) {           
            $this->allData = [];
            $this->createXpath($fileName);
            $this->sweepDivs();
        }    

        // $this->downloadImages();
    }


    public function createXpath($fileName)
    {
        // QuestionsTemp::truncate();
        $this->doc = new DOMDocument();
        libxml_use_internal_errors(true); // Prevents warnings for malformed HTML
        $rawFilePath = __DIR__ . '/texts/' . $fileName;
        $this->doc->loadHTMLFile($rawFilePath);
        File::delete($rawFilePath);
        libxml_clear_errors();

        $this->xpath = new DOMXPath($this->doc);
    }


    public function sweepDivs()
    {
        $divs = $this->xpath->query("//div[contains(@class, 'as-sortable-item')]");
        // $divs = $this->xpath->query("//div[contains(@class, 'textQuestionBox')]");
        // dd(count($divs));
        
        foreach ($divs as $div) 
        {
            $this->setTexts($div);         
            $this->setTypeAndLevel($div);
            $this->setCategoryQuestionId($div);
            $this->getCorrectAnswer();
            if($this->QuestionText != null)
            {
                $this->CreateAllRecord();
            }
            $this->emptyData();
        }
        $this->CreateQuestionsRecord();
    }

    public function setTexts($div)
    {
        $elements = $this->xpath->query(".//p", $div);
        foreach ($elements as $element) {
            $html = $this->doc->saveHTML($element->parentNode);
            // $html = $this->checkForImage($html);
            if($element->getAttribute('ng-bind-html') == "q.QuestionText | unsafe")
            {
                $this->QuestionText = $html;
            }
            if($element->getAttribute('ng-bind-html') == "q.AnswerText | unsafe")
            {                    
                $this->AnswerText = $html;
            }     
            if($element->getAttribute('ng-bind-html') == "q.Choice1 | unsafe")
            {                    
                $this->Choice1 = $html;
            }  
            if($element->getAttribute('ng-bind-html') == "q.Choice2 | unsafe")
            {                    
                $this->Choice2 = $html;
            }             
            if($element->getAttribute('ng-bind-html') == "q.Choice3 | unsafe")
            {                    
                $this->Choice3 = $html;
            } 
            if($element->getAttribute('ng-bind-html') == "q.Choice4 | unsafe")
            {                    
                $this->Choice4 = $html;
            } 
        }

    }

    public function setTypeAndLevel($div)
    {
        $elements = $this->xpath->query(".//*[contains(@class, 'questionLevel')]", $div);
        $type = trim($elements[0]->nodeValue);
        if($type=='تستی')
        {
            $this->type = "test";
        }
        if($type== 'تشریحی')
        {
            $this->type = "descriptive";
        }
        if($type=='درسنامه')
        {
            $this->type = "lesson";
        }
        $levelPersian = $elements[1];
        $this->level = $this->getlevel($levelPersian);

    }
    public function CreateQuestionsRecord()
    {
        
        $chunks = array_chunk($this->allData, 100);
        foreach ($chunks as $chunck) {
            QuestionsTemp::insert($chunck);
            // Question::insert($chunck);
        }
    }
    public function CreateAllRecord()
    {

        $quesion = [];
        $quesion['category_question_id'] = $this->category_question_id; //
        $quesion['front'] = $this->QuestionText;
        $quesion['back'] = $this->AnswerText;
        $quesion['p1'] = $this->Choice1;
        $quesion['p2'] = $this->Choice2;
        $quesion['p3'] = $this->Choice3;
        $quesion['p4'] = $this->Choice4;     
        $quesion['answer'] = $this->correctAnswer;     

        $quesion['percentage'] = $this->level;       //
        $quesion['count'] = 100;
        $quesion['type'] = $this->type;        //
        $quesion['isfree'] = 0;   
        $quesion['created_at'] = now();
        $quesion['updated_at'] = now();
        $this->allData[] = $quesion;
    }

    public function emptyData()
    {
        
        $this->QuestionText = null;
        $this->AnswerText = null;
        $this->Choice1 = null;
        $this->Choice2 = null;
        $this->Choice3 = null;
        $this->Choice4 = null;
        $this->type= null;
        $this->level = 0;
        $this->category_question_id = null;
        $this->correctAnswer = 0;
    }




    public function getlevel($node)
    {
        $html = trim($node->nodeValue);
        $level = 0;
        if(mb_strpos($html, "آسان") !==false)
        {
            $level = 20;
        }
        else if(mb_strpos($html, "متوسط") !==false)
        {
            $level = 40;
        }
        else if(mb_strpos($html, "خیلی دشوار") !==false) // agar doshvar balatar bashe eshtebeh mishe
        {
            $level = 80;
        }  
        else if(mb_strpos($html, "دشوار") !==false)
        {
            $level= 60;
        }
        return $level;
                                        
    }

    public function setCategoryQuestionId($div)
    {
        $this->category_question_id = false;
        $elements = $this->xpath->query(".//*[contains(@class, 'CourseLabel')]", $div);
        foreach ($elements as $element) 
        {
            $element = $element->parentNode;
            $catArray = [];
            foreach ($element->childNodes as $child) {
                $vale = $child->nodeValue;
                $vale = trim($vale);
                if($vale !== "" && strpos($vale, "ObjectTitle") === false)
                {
                    $catArray[] = $vale;
                }
            }
            
            $parentId = null;
            $CatArraySize = count($catArray) -1;
            $catArrayId = [];
            $catArrayParentId = [];

            $cat = CategoryQuestion::where("name", "$catArray[0]")->where("parent_id", $this->payeId)->first();
            // dd($cat);
            // $cat = CategoryQuestion::where('id', 6571)->first(); // in vase zamani hast a id dars ro bedoonam va bar asase id dars bekham soalha ro be in darse khas ezafe konam
            $catArrayId[] = $cat->id;
            $catArrayParentId[] = $cat->parent_id;
            
            for ($i= 1 ; $i <=$CatArraySize  ; $i++) {  
                $oldCat = $cat;               
                $cat = CategoryQuestion::where("name", "$catArray[$i]")->where('parent_id', $cat->id)->first();
                try {
                    $catArrayId[] = $cat->id;
                } catch (\Throwable $th) {
                    // dd(CategoryQuestion::where("name", "$catArray[$i]")->get());
                    dd($catArray[$i], $cat, $oldCat);
                }
                $catArrayParentId[] = $cat->parent_id;
            }
            
            $flag = true;
            for ($i= 0 ; $i < $CatArraySize  ; $i++) {   
                if($catArrayId[$i] != $catArrayParentId[$i+1])
                {
                    $flag = false;
                }            
            }    
            
            if($flag)
            {
                $this->category_question_id =  $catArrayId[$CatArraySize];
            }        
        }
        
    }

    public function getCorrectAnswer()
    {
        if($this->type !="test")
        {
            $this->correctAnswer = null;
            return;
        }
        $this->correctAnswer = 0;
        $search = 'گزین';
        $pos = mb_strpos($this->AnswerText, $search);
        if($pos  !== false)
        {
            $r1 = mb_strpos($this->AnswerText, '۱', $pos);
            $r2 = mb_strpos($this->AnswerText, '۲', $pos);
            $r3 = mb_strpos($this->AnswerText, '۳', $pos);
            $r4 = mb_strpos($this->AnswerText, '۴', $pos);
            $min = 1000000;
            if($r1 !== false && $r1< $min)
            {
                $min = $r1;
                $this->correctAnswer = 1;
            }
            if($r2 !== false && $r2< $min)
            {
                $min = $r2;
                $this->correctAnswer = 2;
            }
            if($r3 !== false && $r3< $min)
            {
                $min = $r3;
                $this->correctAnswer = 3;
            }
            if($r4 !== false && $r4< $min)
            {
                $min = $r4;
                $this->correctAnswer = 4;
            }
        }
    }


    // public function transfer()
    // {
    //     $rows = DB::table('questions_temps')->get()->toArray();
    //     foreach ($rows as $row) {
    //         $row = (array)$row;
    //         unset($row['id']);
    //         DB::table('questions')->insert($row);
    //     }
    //     QuestionsTemp::truncate();
    //     dd($rows);
    // }


    public function transfer()
    {
        
        $questions = QuestionsTemp::all();
        foreach ($questions as $question) {
            $newQuestion = new Question();
            $newQuestion->category_question_id = $question->category_question_id;
            $newQuestion->answer = $question->answer;
            $newQuestion->percentage = $question->percentage;
            $newQuestion->count = $question->count;
            $newQuestion->type = $question->type;
            $newQuestion->save();



            if(($question->id % 100) == 0)
            {
                // dump($question->id);
                dump(QuestionsTemp::count());
            }

            $saveFilePath = $this->getQuestionFilePath($newQuestion);
            $content = [
                "front" => $question->front ,
                "back" => $question->back , 
                "p1" => $question->p1 , 
                "p2" => $question->p2 , 
                "p3" => $question->p3 , 
                "p4" => $question->p4
            ];
            $disk =Storage::disk('questions') ;                                  
            $disk->put($saveFilePath,json_encode($content));
            // dd($saveFilePath);
            // file_put_contents($saveFilePath, json_encode($content));

            // dd($newQuestion);

            $question->delete();
        }
        // QuestionsTemp::truncate();
    }


    // public function checkForImage($html)
    // {
    //     $pos1 = mb_strpos($html, '<span><img class="unique" src=');
    //     if($pos1  !== false)
    //     {
    //         dd(2);
    //         $pos1 = mb_strpos($html, 'https', $pos1);
    //         $pos2 = mb_strpos($html, '></span>', $pos1);
    //         $filePath = mb_substr($html, $pos1, $pos2- $pos1-1);
    //         $newAddress = $this->saveImageFromWeb($filePath);
    //         $html =str_replace($filePath, $newAddress, $html);
    //     }
    //     return $html;
    // }

    // public function saveImageFromWeb($imageUrl)
    // {
    //     $imageContents = false;

    //     $fileName = basename($imageUrl); // Extracts filename from URL
    //     $filePath = $this->folderPath . $fileName;
    //     $savePath = public_path($filePath); // Save in public/images
    //     if(file_exists($savePath))
    //     {
    //         return asset($filePath);
    //     }



    //     try {
    //         $imageContents = file_get_contents($imageUrl);
    //     } catch (\Throwable $th) {
    //         dump($imageUrl);
    //     }
    
    //     if($imageContents !== false)
    //     {
    //         file_put_contents($savePath, $imageContents);
    //     }

    //     return asset($filePath);
    // }

    public function convertPersianToEnglish($number) {
        $persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
       
        return str_replace($persianDigits, $englishDigits, $number);
    }



    public function downloadImagesFromJson()
    {
        $start = 42;
        $space = 10000;
        $mode = "fil";
        if($mode == "Json")
        {
            $questions = Question::where('id', '>=', $start*$space)->where('id', '<=', ($start+1)*$space)->get();
        }
        else
        {
            $json = file_get_contents(getcwd() . '/mydata.json');
            $data = json_decode($json, true);
            dump(count($data));
            $questions = Question::whereIn('id', $data)->get();
        }
        // $questions = Question::all();
        foreach ($questions as $question) {
            if(($question->id % 1000) == 0)
            {
                // dump($question->id);
                dump($question->id);
            }

            $this->setFolderPath($question);

            $saveFilePath = $this->getQuestionFilePath($question);
            $content = [
                "front" => $this->checkForImage($question->front, $question->id) ,
                "back" => $this->checkForImage($question->back, $question->id) , 
                "p1" => $this->checkForImage($question->p1, $question->id) , 
                "p2" => $this->checkForImage($question->p2, $question->id), 
                "p3" => $this->checkForImage($question->p3, $question->id), 
                "p4" => $this->checkForImage($question->p4, $question->id)
            ];
            $disk =Storage::disk('questions') ;                                  
            $disk->put($saveFilePath,json_encode($content));            
        }

        $jsonData = json_encode($this->qIds);
        file_put_contents(getcwd() . '/mydata.json', $jsonData);
    }





    public function downloadImages()
    {
        // limit 100
      // $questions = QuestionsTemp::skip(0)->take(100)->get();
      $mode = 'DB';
      if($mode == 'DB')
      {
        $questions = DB::select(
            <<<SQL
            SELECT * FROM `questions_temps` 
            WHERE front LIKE '%<span><img class="unique" src="https://tx.quiz24.ir%' 
            OR  back LIKE '%<span><img class="unique" src="https://tx.quiz24.ir%' 
            OR  p1 LIKE '%<span><img class="unique" src="https://tx.quiz24.ir%' 
            OR  p2 LIKE '%<span><img class="unique" src="https://tx.quiz24.ir%' 
            OR  p3 LIKE '%<span><img class="unique" src="https://tx.quiz24.ir%' 
            OR  p4 LIKE '%<span><img class="unique" src="https://tx.quiz24.ir%' 
            
            SQL);
            $questions = QuestionsTemp::hydrate($questions);
        $jsonData = json_encode($questions->pluck('id'));
        file_put_contents(getcwd() . '/mydata.json', $jsonData);

      } 
      else
      {
        $json = file_get_contents(getcwd() . '/mydata.json');
        $data = json_decode($json, true);
        $questions = QuestionsTemp::whereIn('id', $data)->get();
      }



        dump($questions->count());




      foreach ($questions as $question) {

        $this->questionId = $question->id;

        $this->setFolderPath($question);



        $question->front = $this->checkForImage($question->front, $question->id);   
        $question->back = $this->checkForImage($question->back, $question->id);        
        $question->p1 = $this->checkForImage($question->p1, $question->id);        
        $question->p2 = $this->checkForImage($question->p2, $question->id);        
        $question->p3 = $this->checkForImage($question->p3, $question->id);        
        $question->p4 = $this->checkForImage($question->p4, $question->id);   
        $question->save();        
      }
    }

    public function setFolderPath($question)
    {
        $catId = $question->category_question_id;
        $categoryQuestion = CategoryQuestion::find($catId); 
        $parentsId =   $categoryQuestion->ancestors()->select('id', 'parent_id')->get()->pluck('id')->toArray();
        $allCatsId = array_merge(array_slice($parentsId, 1), [$catId]);
        $this->folderPath = implode('/', $allCatsId);
        $this->folderPath = "images/Questions/" . $this->folderPath . '/';
        if(!is_dir(public_path($this->folderPath)))
        {
          mkdir($this->folderPath, 0777, true);
        }
    }

   
    public function checkForImage($html, $qId)
    {
        $pos1 = mb_strpos($html, '<span><img class="unique" src="https://tx.quiz24.ir');
        if($pos1  !== false)
        {
            $pos1 = mb_strpos($html, 'https', $pos1);
            $pos2 = mb_strpos($html, '></span>', $pos1);
            $imageUrl = mb_substr($html, $pos1, $pos2- $pos1-1);
            $newAddress = $this->saveImageFromWeb($imageUrl);
            $html =str_replace($imageUrl, $newAddress, $html);
            dump("still-" . $qId);
            $this->qIds[] = $qId;
        }
        return $html;
    }

    public function saveImageFromWeb($imageUrl)
    {
        $imageContents = false;

        // dump($imageUrl);

        $fileName = basename($imageUrl); // Extracts filename from URL
        $filePath = $this->folderPath . $fileName;
        $savePath = public_path($filePath); // Save in public/images
        if(file_exists($savePath))
        {
          return asset($filePath);
        }


        try {
            $imageContents = file_get_contents($imageUrl);
        } catch (\Throwable $th) {
            dump($this->questionId, $imageUrl);
            return $imageUrl;
        }
    
        if($imageContents !== false)
        {
            file_put_contents($savePath, $imageContents);
        }

        return asset($filePath);
    }

    public function saveQuestionsTextes()
    {
        Question::chunk(100, function($questions ){

            foreach ($questions as $quesion) {
                if(($quesion->id % 1000) == 0)
                {
                    dump($quesion->id);
                }
                $saveFilePath = $this->getQuestionFilePath($quesion);
                $content = [
                    "front" => $quesion->front ,
                    "back" => $quesion->back , 
                    "p1" => $quesion->p1 , 
                    "p2" => $quesion->p2 , 
                    "p3" => $quesion->p3 , 
                    "p4" => $quesion->p4
                ];
                file_put_contents($saveFilePath, json_encode($content));
            }
        });
        // $questionText = file_get_contents($saveFilePath);
        // dd(json_decode($questionText)->back);
    }

    public function getQuestionFilePath($question)
    {
        // $questionId = $question->id;
        // $idStr = str_pad($questionId, 7, 0, STR_PAD_LEFT);
        // $pathParts = str_split($idStr);
        // $folderPath = implode('/', $pathParts);
        // $folderPath ="questions/$folderPath/"; 
        // $filePath =  $folderPath. "$questionId.json";
        // if(!is_dir(public_path($folderPath)))
        // {
        //   mkdir($folderPath, 0777, true);
        // }

        // $saveFilePath = public_path($filePath); // Save in public/images
        // return $saveFilePath;      
        
        $questionId = $question->id;
        $idStr = str_pad($questionId, 7, '0', STR_PAD_LEFT);
        $pathParts = str_split($idStr);
        $folderPath = implode('/', $pathParts);
        return "{$folderPath}/{$questionId}.json"; // Storage path
    }

}

