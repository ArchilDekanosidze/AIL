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
use Illuminate\Support\Facades\Storage;

class JozveController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'category_jozve_id' => 'required|exists:category_jozves,id',
            // add other fields here...
            ], [
            'category_jozve_id.required' => 'لطفاً یک دسته‌بندی انتخاب کنید.',
            'category_jozve_id.exists' => 'دسته‌بندی انتخاب شده معتبر نیست.',
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file_path' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,zip|max:20480', // 20MB max
            'category_jozve_id' => 'required|exists:category_jozves,id',
        ]);


        $path = $request->file('file_path')->store('jozves', 'private');

        Jozve::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'category_jozve_id' => $request->category_jozve_id,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'جزوه با موفقیت اضافه شد.');
    }

    public function download(Jozve $jozve)
    {
        // Optional: check if user must be logged in
        if (!auth()->check()) {
            // abort(403, 'برای دانلود باید وارد شوید.');
        }

        // Check file exists
        if (!Storage::disk('private')->exists($jozve->file_path)) {
            abort(404, 'فایل یافت نشد.');
        }

        $filename = $jozve->title . '.' . pathinfo($jozve->file_path, PATHINFO_EXTENSION);

        return Storage::disk('private')->download($jozve->file_path, $filename);
    }


}
