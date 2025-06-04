<?php

namespace App\Models;

use App\Models\GamBeGam;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CategoryGamBeGam extends Model
{
    use HasFactory , NodeTrait, SoftDeletes;

        public function gamBeGams()
    {
        return $this->hasMany(GamBeGam::class);
    }
}
