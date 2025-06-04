<?php
namespace App\Http\Controllers\Category;


use App\Models\Book;
use App\Models\Category;
use App\Models\CategoryBook;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BookController extends Controller
{
    public function show(Book $book)
    {
        $data['id'] = $book->id;
        $data['title'] = $book->title;
        $data['image'] = $book->image;
        $data['fileName'] =  $book->fileName;
        $data['fileUrl'] = $book->fileUrl;
        $data['fileSize'] = $book->fileSize;
        $data['year'] = $book->year;
        $data['code'] = $book->code;
        $allAncestorPath = [];

        $cats = $book->categories;
        foreach ($cats as $cat) {
            $ancestorName =  $cat->ancestors()->get()->pluck("name");
            $ancestorName[] = $cat->name;
            $ancestorName->shift();
            $ancestorPath = implode(" -> ", $ancestorName->toArray());
            $allAncestorPath[] = $ancestorPath;
        }
        $data['allAncestorPath'] = $allAncestorPath;

        $parts = $book->parts()->get();
        $newParts = [];
        foreach ($parts as $part) {
            $newPart['name'] = $part->name;
            $newPart['url'] = $part->url;
            $newPart['size'] = $part->size;
            $newParts[] = $newPart;
        }
        $data['parts'] = $newParts;        

        return view('category.categoryBook.show', compact('data'));
    }    
}
