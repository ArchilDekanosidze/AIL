<?php
namespace App\Http\Controllers\Category;


use App\Models\Book;
use App\Models\Category;
use App\Models\CategoryBook;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryBookController extends Controller
{
    public function index()
    {                   
        return view('category.categoryBook.index'); 
    }

    public function getChildren($parentId)
    {
        // dd($parentId);
        $children = CategoryBook::where('parent_id', $parentId)->get(['id', 'name']);
        return response()->json($children);
    }

    public function getBooks(Request $request)
    {
        $query = Book::query();
        $year = $request->year;
        $categoryId = $request->category_id;
        // Filter by year if selected
        if ($request->filled('year')) {
            $query->where('year', $year);
        }

        // dd($year, $categoryId);

        if ($request->filled('category_id')) {
            $category = CategoryBook::find($categoryId);

            if ($category) {
                $ids = $category->descendants()->pluck('id')->push($category->id);

                $query->whereHas('categories', function ($q) use ($ids) {
                    $q->whereIn('category_books.id', $ids); // Ensure table name is correct
                });
            }
        }

        $books = $query->paginate(20); // 12 books per page

        $html = view('partials.books', compact('books'))->render();

        return response()->json([
            'html' => $html,
            'pagination' => $books->links()->render(), // no need for (string) cast
        ]);



       
    }
    
}
