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
use App\Models\Exam;

class CategoryExamController extends Controller
{
    public function index()
    {                   
        return view('category.categoryExam.index');
    }

    public function getChildren($parentId)
    {
        // dd($parentId);
        $children = CategoryExam::where('parent_id', $parentId)->get(['id', 'name']);
        return response()->json($children);
    }

    public function getExam(Request $request)
    {
        $query = Exam::query();
        $categoryId = $request->category_id;

        if ($categoryId) {
            $category = CategoryExam::find($categoryId);

            if ($category) {
                $ids = $category->descendants()->pluck('id')->push($category->id);
                $query->whereIn('category_exam_id', $ids);
            }
        }

        $exams = $query->paginate(20);

        $html = view('partials.exams', compact('exams'))->render();

        return response()->json([
            'html' => $html,
            'pagination' => $exams->links()->render(),
        ]);
    }

}
