<?php

namespace App\Http\Controllers\Admin;

use App\Models\CategoryQuestion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class AdminQuestionController extends Controller
{

    public function index(CategoryQuestion $category)
    {      
        // $path = $category->path();
        // $directCats =  $category->children()->get();
        // return view('admin.question.index', compact('directCats', 'path'));
    }

    public function create()
    {
        $categories = CategoryQuestion::all();
        return view('admin.question.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // $newCat  = CategoryQuestion::create(['name' => $request->newCategory]);
        // $parentCat = CategoryQuestion::find($request->categorySelect);
        // $newCat->appendToNode($parentCat)->save();
        // return back();
    }

    public function edit(CategoryQuestion $currentCategory)
    {
        // $categories = CategoryQuestion::all();
        // return view('admin.question.edit', compact('categories', 'currentCategory'));
    }

    public function update(Request $request, CategoryQuestion $currentCategory)
    {
        // $parentCat = CategoryQuestion::find($request->categorySelect);
        // $currentCategory->appendToNode($parentCat)->save();
        // return back();
    }

    public function delete(Request $request, CategoryQuestion $currentCategory)
    {
        // $allQuestionCount = $currentCategory->allQuestion();
        // if($allQuestionCount->count() == 0)
        // {
        //     $currentCategory->delete();
        // }
        // return back();
    }
}

