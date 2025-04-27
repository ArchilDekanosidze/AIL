<?php

namespace App\Models;

use App\Models\Book;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryBook extends Model 
{
    use HasFactory, NodeTrait;

    protected $fillable = ['name'];
 
    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function path()
    {         
        $ancestorName =  $this->ancestors()->get()->pluck("name");
        $ancestorName[] = $this->name;
        return implode(" -> ", $ancestorName->toArray());
    }
}
