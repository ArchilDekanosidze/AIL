<?php

namespace App\Models;

use App\Models\User;
use App\Models\CategoryFree;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FreeFile extends Model
{
    use HasFactory, SoftDeletes;

     protected $fillable = ['title', 'description', 'file_path', 'category_free_id', 'user_id'];

    public function categoryFree()
    {
        return $this->belongsTo(CategoryFree::class, 'category_free_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
