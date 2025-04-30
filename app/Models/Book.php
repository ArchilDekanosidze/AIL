<?php

namespace App\Models;

use App\Models\BookPart;
use App\Models\CategoryBook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'image', 'file', 'year', 'code'];

    public function categories()
    {
        return $this->belongsToMany(CategoryBook::class, 'book_category_book');
    }

    public function parts()
    {
        return $this->hasMany(BookPart::class);
    }
}
