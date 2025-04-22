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

class AdminImportCategoryController extends Controller
{
    private $doc;
    private $xpath;
    private $parentCategory;
    private $payeId = "18";



    public function index()
    {       
        $this->createXpath();
        $rootUl = $this->xpath->query("//ul")->item(0);
        $nestedCategories = $this->traverseTree($rootUl);
        $this->parentCategory = CategoryQuestion::find($this->payeId);
        $this->saveCategories($nestedCategories);
        dd($nestedCategories);
    }


    public function createXpath()
    {
        $html = file_get_contents(__DIR__ . '/category.html');
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        $dom = new DOMDocument;
        @$dom->loadHTML($html, LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $this->xpath = new DOMXPath($dom);
    }

    function traverseTree($node) {
        $categories = [];    
        foreach ($node->childNodes as $child) {
            if ($child->nodeName === 'li') {
                // Get the category title
                $titleNode = $this->xpath->query('.//span[@class="fancytree-title"]', $child)->item(0);
                if ($titleNode) {
                    $category = [
                        'name' => trim($titleNode->nodeValue),
                        'children' => [] // Placeholder for subcategories
                    ];
    
                    // Check if this <li> has a nested <ul>
                    foreach ($child->childNodes as $subChild) {
                        if ($subChild->nodeName === 'ul') {
                            $category['children'] = $this->traverseTree($subChild); // Recursively get subcategories
                        }
                    }
    
                    $categories[] = $category;
                }
            }
        }
    
        return $categories;
    }

    public function saveCategories($categories, $parent = null) {
        foreach ($categories as $categoryData) {
            // Create category and attach it to parent if provided
            $category = new CategoryQuestion(['name' => $categoryData['name']]);
           
            if ($parent) {
                $parent->appendNode($category);
            } else {
                $this->parentCategory->appendNode($category);
            }
    
            // Recursively save subcategories
            if (!empty($categoryData['children'])) {
                $this->saveCategories($categoryData['children'], $category);
            }
        }
    } 
    

}

