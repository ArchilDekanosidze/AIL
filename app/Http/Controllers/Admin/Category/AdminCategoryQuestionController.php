<?php

namespace App\Http\Controllers\Admin\Category;

use App\Models\CategoryQuestion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class AdminCategoryQuestionController extends Controller
{

    public function index(CategoryQuestion $category)
    {      
        $path = $category->path();
        $directCats =  $category->children()->get();
        return view('admin.category.categoryQuestion.index', compact('directCats', 'path'));
    }

    public function create()
    {
        $categories = CategoryQuestion::all();
        return view('admin.category.categoryQuestion.create', compact('categories'));
    }

    public function createSubCat($categorySelect)
    {
        $categories = CategoryQuestion::all();
        Session::put("categorySelect", $categorySelect);
        return view('admin.category.categoryQuestion.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $newCat  = CategoryQuestion::create(['name' => $request->newCategory]);
        $parentCat = CategoryQuestion::find($request->categorySelect);
        $newCat->appendToNode($parentCat)->save();
        return redirect()->back()->with("categorySelect", $request->categorySelect);
    }

    public function edit(CategoryQuestion $currentCategory)
    {
        $categories = CategoryQuestion::all();
        return view('admin.category.categoryQuestion.edit', compact('categories', 'currentCategory'));
    }

    public function update(Request $request, CategoryQuestion $currentCategory)
    {
        $parentCat = CategoryQuestion::find($request->categorySelect);
        $currentCategory->name =  $request->currentCategoryName;
        $parentCat->save();
        $currentCategory->appendToNode($parentCat)->save();
        return back();
    }

    public function delete(Request $request, CategoryQuestion $currentCategory)
    {
        $allQuestionCount = $currentCategory->allQuestion();
        if($allQuestionCount->count() == 0)
        {
            $currentCategory->delete();
        }
        return back()->withErrors("این دسته بندی حاوی سوال می باشد");
    }
}

