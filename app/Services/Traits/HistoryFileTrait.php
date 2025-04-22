<?php
namespace App\Services\Traits;

use Illuminate\Support\Facades\Storage;

trait HistoryFileTrait
{
    public function getHistoryFolderPath($id)
    {
        $idStr = str_pad($id, 9, '0', STR_PAD_LEFT);
        $pathParts = str_split($idStr);
        $folderPath = implode('/', $pathParts);
        return "user_category_question_history/$folderPath/";
    }

    public function getHistory($id)
    {
        $filePath = $this->getHistoryFolderPath($id) . "{$id}.json";
        
        // Using Storage::disk to check if the file exists and retrieve its content
        if (Storage::disk('private')->exists($filePath)) {
            $fileContent = Storage::disk('private')->get($filePath);
            return json_decode($fileContent, true);
        }
        return null;
    }

    public function saveHistory($id, $content)
    {
        $folderPath = $this->getHistoryFolderPath($id);

        // Ensure the directory exists
        if (!Storage::disk('private')->exists($folderPath)) {
            Storage::disk('private')->makeDirectory($folderPath);
        }

        $filePath = $folderPath . "$id.json";

        // Save content to the file
        Storage::disk('private')->put($filePath, json_encode($content));
    }

    // public function public_html_path($path = '')
    // {
    //     if(app()->environment('local'))
    //     {
    //         return public_path($path);
    //     }
    //     return base_path('../public_html' . ($path ? '/' . $path : ''));
    // }
}