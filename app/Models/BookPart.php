<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookPart extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'url', 'size', 'book_id'];

    // Define the relationship to the Book model
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
