<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jozve extends Model
{
    use HasFactory;

     protected $fillable = ['title', 'description', 'file_path', 'category_jozve_id', 'user_id'];

    public function categoryJozve()
    {
        return $this->belongsTo(CategoryJozve::class, 'category_jozve_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
