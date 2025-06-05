<?php

namespace App\Models;

use App\Models\Jozve;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryJozve extends Model
{
    use HasFactory, NodeTrait, SoftDeletes;

    protected $fillable = ['name'];

    public function jozves()
    {
        return $this->hasMany(Jozve::class);
    }
}
