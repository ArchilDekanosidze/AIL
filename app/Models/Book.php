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
        return $this->belongsToMany(
            CategoryBook::class,
            'book_category_book',          // Pivot table name
            'book_id',                     // Foreign key on the pivot table pointing to Book
            'category_book_id'             // Foreign key on the pivot table pointing to CategoryBook
        );
    }

    public function parts()
    {
        return $this->hasMany(BookPart::class);
    }
}
