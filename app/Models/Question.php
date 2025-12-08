<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\Vote;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'tag_id',
        'category_question_id',
        'answer',
        'percentage',
        'count',
        'type',
        'isfree',
        'isdeactive',
    ];

    protected $appends = ['front', 'back', 'p1', 'p2', 'p3', 'p4'];    

    protected function storage()
    {
        return app(\App\Services\Uploader\StorageManagerService::class);
    }

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

    public function scopeActive($query)
    {
        return $query->where('isdeactive', '0');
    }



    public function getFilePath()
    {
        $idStr = str_pad($this->id, 7, '0', STR_PAD_LEFT);
        $pathParts = str_split($idStr);
        $folderPath = implode('/', $pathParts);
        return "{$folderPath}/{$this->id}.json"; // Storage path
    }

    // Fetch content from file
    private function getContent()
    {
        $filePath = $this->getFilePath(); // Store in public storage
        $storage = $this->storage();

        if ($storage->isFileExists($filePath, 'questions', true)) {
            $content = Storage::disk('questions')->get($filePath);
            return json_decode($content, true);
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

    // public function public_html_path($path = '')
    // {
    //     if(app()->environment('local'))
    //     {
    //         return public_path($path);
    //     }
    //     return base_path('../public_html' . ($path ? '/' . $path : ''));
    // }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }


    public function votes() {
        return $this->hasMany(Vote::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

}
