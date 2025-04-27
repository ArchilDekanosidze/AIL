<?php

namespace App\Models;

use App\Models\CategoryBook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'image', 'file', 'year', 'code'];

    public function category()
    {
        return $this->belongsTo(CategoryBook::class);
    }
}
