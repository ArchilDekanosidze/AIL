<?php
namespace App\Http\Controllers\Admin\Import;

use DOMXPath;
use DOMDocument;
use App\Models\Question;
use App\Models\QuestionsTemp;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\CategoryQuestion;
use DOMElement;
use Illuminate\Support\Facades\Response;
use PhpParser\Node\Stmt\Catch_;

class AdminImportController extends Controller
{
    private $doc;
    private $xpath;
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
    private $payeId = "14";

    private  $folderPath = 'images' . '/' . '9' . '/' . 'ghoran' . '/';

    public function import()
    {       
        //copy(document.querySelector(.firefox).outerHTML)
        // این دو تای پایینی رو اصلاح کن که بیشترین مقدار کتگوری ای رو نگه داره
        // SELECT * FROM `questions` WHERE `id` NOT IN( SELECT `id` FROM ( SELECT MIN(`id`) as id FROM `questions` GROUP BY `front` ) as temp ); 
        // DELETE FROM `questions` WHERE `id` NOT IN( SELECT `id` FROM ( SELECT MIN(`id`) as id FROM `questions` GROUP BY `front` ) as temp ); 
        $this->createXpath();
        $this->sweepDivs();
    }


    public function createXpath()
    {
        QuestionsTemp::truncate();
        $this->doc = new DOMDocument();
        libxml_use_internal_errors(true); // Prevents warnings for malformed HTML
        $this->doc->loadHTMLFile(__DIR__ . '/import.html');
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
                $this->CreateQuestionRecord();
            }
            $this->emptyData();
        }
    }

    public function setTexts($div)
    {
        $elements = $this->xpath->query(".//p", $div);
        foreach ($elements as $element) {
            $html = $this->doc->saveHTML($element->parentNode);
            $html = $this->checkForImage($html);
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

    public function CreateQuestionRecord()
    {

        $quesion = new QuestionsTemp();
        $quesion->category_question_id = $this->category_question_id; //
        $quesion->front = $this->QuestionText;
        $quesion->back = $this->AnswerText;
        $quesion->p1 = $this->Choice1;
        $quesion->p2 = $this->Choice2;
        $quesion->p3 = $this->Choice3;
        $quesion->p4 = $this->Choice4;     
        $quesion->answer = $this->correctAnswer;     

        $quesion->percentage = $this->level;       //
        $quesion->count = 100;
        $quesion->type = $this->type;        //
        $quesion->isfree = 0;   
        $quesion->timestamps = now();
        try {
            $quesion->save();                    
        } catch (\Throwable $th) {
            dd($this->category_question_id);
        }
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
        $rows = DB::table('questions_temps')->get();
        $insertData = $rows->map(function($row){
            $row = (array)$row;
            unset($row['id']);
            return $row;
        });

        $insertData->chunk(100)->each(function($chunck){
            DB::table('questions')->insert($chunck->toArray());
        });

        QuestionsTemp::truncate();
        dd($rows);
    }


    public function checkForImage($html)
    {
        $pos1 = mb_strpos($html, '<span><img class="unique" src=');
        if($pos1  !== false)
        {
            $pos1 = mb_strpos($html, 'https', $pos1);
            $pos2 = mb_strpos($html, '></span>', $pos1);
            $filePath = mb_substr($html, $pos1, $pos2- $pos1-1);
            $newAddress = $this->saveImageFromWeb($filePath);
            $html =str_replace($filePath, $newAddress, $html);
        }
        return $html;
    }

    public function saveImageFromWeb($imageUrl)
    {
        $imageContents = false;

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
            dump($imageUrl);
        }

        // if ($imageContents === false) {
        //     return "Failed to download image.";
        // }
    

        if($imageContents !== false)
        {
            file_put_contents($savePath, $imageContents);
        }

        return asset($filePath);
    }

    public function convertPersianToEnglish($number) {
        $persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
       
        return str_replace($persianDigits, $englishDigits, $number);
    }
}

