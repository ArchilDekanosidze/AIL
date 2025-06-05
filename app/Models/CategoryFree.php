<?php

namespace App\Models;

use App\Models\FreeFile;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryFree extends Model
{
    use HasFactory, NodeTrait, SoftDeletes;

    protected $fillable = ['name'];

    public function freeFiles()
    {
        return $this->hasMany(FreeFile::class, 'category_free_id');
    }
}
