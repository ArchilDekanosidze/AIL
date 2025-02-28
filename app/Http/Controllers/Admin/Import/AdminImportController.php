<?php
namespace App\Http\Controllers\Admin\Import;

use DOMXPath;
use DOMDocument;
use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionsTemp;
use Illuminate\Support\Facades\Response;

class AdminImportController extends Controller
{
    private $doc;
    private $xpath;
    private $QuestionText = [];
    private $AnswerText = [];
    private $Choice1=[];
    private $Choice2 =[];
    private $Choice3=[];
    private $Choice4=[];
    private $category_question_id=25;
    private $percentage = 20;
    private $type = "test";
    private  $folderPath = 'images' . '/' . 'دهم-تجربی' . '/' . 'فیزیک' . '/' . '1' . '/';

    public function importTest()
    {       
        $this->createXpathTest();
        $this->createTestQuestionArray();
        $this->CreateQuestionTestRecords();                    
    }

    public function importTashrihi()
    {
        $this->createXpathTashrihi();
        $this->createTashrihiQuestionArray();
        $this->CreateQuestionTashrihiRecords();

    }

    public function createXpathTest()
    {
        Question::truncate();
        $this->doc = new DOMDocument();
        libxml_use_internal_errors(true); // Prevents warnings for malformed HTML
        $this->doc->loadHTMLFile(__DIR__ . '/test.html');
        libxml_clear_errors();

        $this->xpath = new DOMXPath($this->doc);
    }

    public function createXpathTashrihi()
    {
        Question::truncate();
        $this->doc = new DOMDocument();
        libxml_use_internal_errors(true); // Prevents warnings for malformed HTML
        $this->doc->loadHTMLFile(__DIR__ . '/tashrihi.html');
        libxml_clear_errors();

        $this->xpath = new DOMXPath($this->doc);
    }

    public function createTestQuestionArray()
    {
        $elements = $this->xpath->query("//*[@ng-bind-html]");
        foreach ($elements as $p) 
        {
            $html = $this->doc->saveHTML($p->parentNode);
            if($p->hasAttribute('ng-bind-html'))
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
                if($p->getAttribute('ng-bind-html') == "q.QuestionText | unsafe")
                {                    
                    $this->QuestionText[] = $html;
                }
                if($p->getAttribute('ng-bind-html') == "q.AnswerText | unsafe")
                {                    
                    $this->AnswerText[] = $html;
                }     
                if($p->getAttribute('ng-bind-html') == "q.Choice1 | unsafe")
                {                    
                    $this->Choice1[] = $html;
                }  
                if($p->getAttribute('ng-bind-html') == "q.Choice2 | unsafe")
                {                    
                    $this->Choice2[] = $html;
                }             
                if($p->getAttribute('ng-bind-html') == "q.Choice3 | unsafe")
                {                    
                    $this->Choice3[] = $html;
                } 
                if($p->getAttribute('ng-bind-html') == "q.Choice4 | unsafe")
                {                    
                    $this->Choice4[] = $html;
                } 
            }
        }
    }
    public function createTashrihiQuestionArray()
    {
        $elements = $this->xpath->query("//*[@ng-bind-html]");
        foreach ($elements as $p) 
        {
            $html = $this->doc->saveHTML($p->parentNode);
            if($p->hasAttribute('ng-bind-html'))
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
                if($p->getAttribute('ng-bind-html') == "q.QuestionText | unsafe")
                {                    
                    $this->QuestionText[] = $html;
                }
                if($p->getAttribute('ng-bind-html') == "q.AnswerText | unsafe")
                {                    
                    $this->AnswerText[] = $html;
                }     
            }
        }
    }

    public function CreateQuestionTestRecords()
    {

        for ($i=0; $i < count($this->QuestionText); $i++) { 
            $quesion = new Question();
            $quesion->category_question_id = $this->category_question_id; //
            $quesion->front = $this->QuestionText[$i];
            $quesion->back = $this->AnswerText[$i];
            $quesion->p1 = $this->Choice1[$i];
            $quesion->p2 = $this->Choice2[$i];
            $quesion->p3 = $this->Choice3[$i];
            $quesion->p4 = $this->Choice4[$i];     
            $myAnswer  = $this->AnswerText[$i];
            $search = 'گزینه';
            $pos = mb_strpos($myAnswer, $search);
            if($pos  !== false)
            {
                $result = mb_substr($myAnswer, $pos + strlen($search)-4 , 1);
                $result = $this->convertPersianToEnglish($result);
                if($result == 1 || $result == 2 || $result == 3 || $result == 4)
                {
                    $quesion->answer = $result;
                }
                else
                {
                    $search = 'گزینه';
                    $pos = mb_strpos($myAnswer, $search);        
                    $result = mb_substr($myAnswer, $pos + strlen($search) +40 , 1);
                    $result = $this->convertPersianToEnglish($result);
                    $quesion->answer = $result;               
                }
            }
            else
            {
                $quesion->answer = 0;           
            }
            $quesion->percentage = $this->percentage;       //
            $quesion->count = 100;
            $quesion->type = $this->type;        //
            $quesion->isfree = 0;   
            $quesion->timestamps = now();
            $quesion->save();
        }
    }

    public function CreateQuestionTashrihiRecords()
    {

        for ($i=0; $i < count($this->QuestionText); $i++) { 
            $quesion = new Question();
            $quesion->category_question_id = $this->category_question_id; //
            $quesion->front = $this->QuestionText[$i];
            $quesion->back = $this->AnswerText[$i];

            $quesion->percentage = $this->percentage;       //
            $quesion->count = 100;
            $quesion->type = $this->type;        //
            $quesion->isfree = 0;   
            $quesion->timestamps = now();
            $quesion->save();
        }
    }

    public function convertPersianToEnglish($number) {
        $persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
       
        return str_replace($persianDigits, $englishDigits, $number);
    }

    public function saveImageFromWeb($imageUrl)
    {
        $imageContents = file_get_contents($imageUrl);

        if ($imageContents === false) {
            return "Failed to download image.";
        }
    
        $fileName = basename($imageUrl); // Extracts filename from URL
        $filePath = $this->folderPath . $fileName;
        $savePath = public_path($filePath); // Save in public/images

        file_put_contents($savePath, $imageContents);

        return asset($filePath);
    }
}

