<?php
namespace App\Http\Controllers\Category;


use App\Models\Book;
use App\Models\Category;
use App\Models\GamBeGam;
use App\Models\CategoryBook;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CategoryGamBeGam;

class CategoryGambeGamController extends Controller
{
    public function index()
    {                   
        return view('category.categoryGambeGam.index');
    }

    public function getChildren($parentId)
    {
        // dd($parentId);
        $children = CategoryGamBeGam::where('parent_id', $parentId)->get(['id', 'name']);
        return response()->json($children);
    }

    public function getGambeGams(Request $request)
    {
        $query = GambeGam::query();
        $categoryId = $request->category_id;

        if ($categoryId) {
            $category = CategoryGamBeGam::find($categoryId);

            if ($category) {
                $ids = $category->descendants()->pluck('id')->push($category->id);
                $query->whereIn('category_gam_be_gam_id', $ids);
            }
        }

        $gams = $query->paginate(20);

        $html = view('partials.gambeGams', compact('gams'))->render();

        return response()->json([
            'html' => $html,
            'pagination' => $gams->links()->render(),
        ]);
    }

}
