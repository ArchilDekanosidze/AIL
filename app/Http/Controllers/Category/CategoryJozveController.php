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
use App\Models\CategoryJozve;
use App\Models\Exam;
use App\Models\Jozve;

class CategoryJozveController extends Controller
{
    public function index()
    {                   
        return view('category.categoryJozve.index');
    }

    public function getChildren($parentId)
    {
        // dd($parentId);
        $children = CategoryJozve::where('parent_id', $parentId)->get(['id', 'name']);
        return response()->json($children);
    }

    public function getJozve(Request $request)
    {
        $query = Jozve::query();

        if ($categoryId = $request->category_id) {
            $query->where('category_jozve_id', $categoryId);
        }

        if ($search = $request->search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $jozves = $query->paginate(12);

        $html = view('partials.jozves', compact('jozves'))->render();

        return response()->json([
            'html' => $html,
            'pagination' => $jozves->links()->render(),
        ]);
    }


}
