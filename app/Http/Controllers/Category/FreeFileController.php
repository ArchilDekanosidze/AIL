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
use App\Models\FreeFile;
use App\Models\Jozve;
use Illuminate\Support\Facades\Storage;

class FreeFileController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'category_free_id' => 'required|exists:category_frees,id',
            // add other fields here...
            ], [
            'category_free_id.required' => 'لطفاً یک دسته‌بندی انتخاب کنید.',
            'category_free_id.exists' => 'دسته‌بندی انتخاب شده معتبر نیست.',
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file_path' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,zip|max:20480', // 20MB max
            'category_free_id' => 'required|exists:category_frees,id',
        ]);


        $path = $request->file('file_path')->store('frees', 'private');

        FreeFile::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'category_free_id' => $request->category_free_id,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'فایل با موفقیت اضافه شد.');
    }

    public function download(FreeFile $freeFile)
    {
        // Optional: check if user must be logged in
        if (!auth()->check()) {
            // abort(403, 'برای دانلود باید وارد شوید.');
        }

        // Check file exists
        if (!Storage::disk('private')->exists($freeFile->file_path)) {
            abort(404, 'فایل یافت نشد.');
        }

        $filename = $freeFile->title . '.' . pathinfo($freeFile->file_path, PATHINFO_EXTENSION);

        return Storage::disk('private')->download($freeFile->file_path, $filename);
    }


}
