<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        "category_question_id",
        "front",
        "back",
        "p1",
        "p2",
        "p3",
        "p4",
        "answer",
        "percentage",
        "count"];

    protected $appends = ['front', 'back', 'p1', 'p2', 'p3', 'p4'];    

    public function category()
    {
        return $this->belongsTo(CategoryQuestion::class, 'category_question_question');
    }

    public function scopeTest($query)
    {
        return $query->where('type', 'test');
    }
    public function scopeDescriptive($query)
    {
        return $query->where('type', 'descriptive');
    }
    public function scopeLesson($query)
    {
        return $query->where('type', 'lesson');
    }



    public function getFilePath()
    {
        $idStr = str_pad($this->id, 7, '0', STR_PAD_LEFT);
        $pathParts = str_split($idStr);
        $folderPath = implode('/', $pathParts);
        return $this->public_html_path("questions/$folderPath/{$this->id}.json");
    }

    // Fetch content from file
    private function getContent()
    {
        $filePath = $this->getFilePath(); // Store in public storage
        if ( file_exists($filePath)) {
            return json_decode(file_get_contents($filePath), true);
        }
        return [];
    }

    // Define accessors
    public function getFrontAttribute()
    {
        $content = $this->getContent();
        return $content['front'] ?? null;
    }

    public function getBackAttribute()
    {
        $content = $this->getContent();
        return $content['back'] ?? null;
    }

    public function getP1Attribute()
    {
        $content = $this->getContent();
        return $content['p1'] ?? null;
    }

    public function getP2Attribute()
    {
        $content = $this->getContent();
        return $content['p2'] ?? null;
    }

    public function getP3Attribute()
    {
        $content = $this->getContent();
        return $content['p3'] ?? null;
    }

    public function getP4Attribute()
    {
        $content = $this->getContent();
        return $content['p4'] ?? null;
    }

    public function public_html_path($path = '')
    {
        if(app()->environment('local'))
        {
            return public_path($path);
        }
        return base_path('../public_html' . ($path ? '/' . $path : ''));
    }
}
