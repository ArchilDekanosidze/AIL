<?php
namespace App\Services\Traits;

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
        $filePath = $this->public_html_path($this->getHistoryFolderPath($id) . "{$id}.json");
        if(file_exists($filePath))
        {
            $fileContent =  file_get_contents($filePath);
            // dd($fileContent, $id, $filePath);
            return json_decode($fileContent, true);
        }
        return null;
    }

    public function saveHistory($id, $content)
    {
        $folderPath = $this->getHistoryFolderPath($id);
        if(!is_dir($this->public_html_path($folderPath)))
        {
          mkdir($folderPath, 0777, true);
        }

        $filePath =  $folderPath. "$id.json";

        $filePath = $this->public_html_path($filePath); // Save in public/images

        file_put_contents($filePath,json_encode($content));
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