<?php
namespace App\Http\Controllers\Upload;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Services\Uploader\Image\ImageInterventionService;

 


class CkEditorUploaderController extends Controller 
{
    public function upload(Request $request, ImageInterventionService $imageInterventionService)
    {
        if ($request->hasFile('upload')) { 
            $file = $request->file('upload');
            // $imageInterventionService->setExclusiveDirectory('images/ckeditor');
            $imageInterventionService->setKeepAspectRatio(); 
            $imageInterventionService->setDisk('ckeditor');
            $filename = $imageInterventionService->save($file);  
            $url = Storage::url($filename);
            $url = url('/ckeditor/file/' . $filename);
            return response()->json([
                'uploaded' => 1,
                'fileName' => $filename,
                'url' => $url
            ]);
        }
  
        return response()->json(['uploaded' => 0]);
    }

    public function urlMaker($filename)
    {
        $filePath = storage_path('app/private/ckeditor/' . $filename);

        // Check if the file exists
        if (file_exists($filePath)) {
            // Use asset() to generate a URL for the public path
            return response()->file($filePath);
        }
    
        return abort(404);  // Return 404 if file doesn't exist
    }
}
