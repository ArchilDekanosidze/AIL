<?php

namespace App\Http\Controllers\Admin\Category;

use Illuminate\Http\Request;
use App\Models\CategoryQuestion;
use App\Http\Controllers\Controller;
use App\Models\CategoryBook;
use Illuminate\Support\Facades\Session;

class AdminCategoryController extends Controller
{

    public function index(CategoryBook $category)
    {      
        $path = $category->path();
        $directCats =  $category->children()->get();
        return view('admin.category.category.index', compact('directCats', 'path'));
    }

    public function create()
    {
        $categories = CategoryBook::all();
        return view('admin.category.category.create', compact('categories'));
    }

    public function createSubCat($categorySelect)
    {
        $categories = CategoryBook::all();
        Session::put("categorySelect", $categorySelect);
        return view('admin.category.category.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $newCat  = CategoryBook::create(['name' => $request->newCategory]);
        $parentCat = CategoryBook::find($request->categorySelect);
        $newCat->appendToNode($parentCat)->save();
        return redirect()->back()->with("categorySelect", $request->categorySelect);
    }

    public function edit(CategoryBook $currentCategory)
    {
        $categories = CategoryBook::all();
        return view('admin.category.category.edit', compact('categories', 'currentCategory'));
    }

    public function update(Request $request, CategoryBook $currentCategory)
    {
        $parentCat = CategoryBook::find($request->categorySelect);
        $currentCategory->name =  $request->currentCategoryName;
        $parentCat->save();
        $currentCategory->appendToNode($parentCat)->save();
        return back();
    }

    public function delete(Request $request, CategoryBook $currentCategory)
    {
            $currentCategory->delete();
    }
}

