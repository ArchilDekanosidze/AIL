<?php
namespace App\Http\Controllers\Category;


use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class CategoryBookController extends Controller
{
    public function index(Category $categoryBook, Request $request)
    {                   
        dd(2);
        // $directCats = $this->categoriesQuestionService->getDirectcats($currentCategory);   
        // $ancestor = $this->categoriesQuestionService->getAncestor($currentCategory);   


        return view('category.categoryBook.index', compact('currentCategory', 'directCats', 'ancestor'));
    }
    
}
