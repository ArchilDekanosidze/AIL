<?php
namespace App\Http\Controllers\Category;


use App\Models\Book;
use App\Models\Category;
use App\Models\GamBeGam;
use App\Models\CategoryBook;
use App\Models\CategoryExam;
use Illuminate\Http\Request;
use App\Models\CategoryGamBeGam;
use App\Http\Controllers\Controller;
use App\Models\CategoryFree;
use App\Models\CategoryJozve;
use App\Models\Exam;
use App\Models\FreeFile;
use App\Models\Jozve;

class CategoryFreeController extends Controller
{
    public function index()
    {                   
        return view('category.categoryFree.index');
    }

    public function getChildren($parentId)
    {
        // dd($parentId);
        $children = CategoryFree::where('parent_id', $parentId)->get(['id', 'name']);
        return response()->json($children);
    }

    public function getFreeFile(Request $request)
    {
        $query = FreeFile::query();

        if ($categoryId = $request->category_id) {
            $query->where('category_free_id', $categoryId);
        }

        if ($search = $request->search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $freeFiles = $query->paginate(12);

        $html = view('partials.freeFiles', compact('freeFiles'))->render();

        return response()->json([
            'html' => $html,
            'pagination' => $freeFiles->links()->render(),
        ]);
    }


}
