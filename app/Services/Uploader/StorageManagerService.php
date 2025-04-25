<?php
namespace App\Services\Uploader;


use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StorageManagerService
{
    
    public function putFileAsPrivate(string $name, UploadedFile|File $file, string $diskName)
    {
        return Storage::disk($diskName)->putFileAs('', $file, $name);
    }

    public function putFileAsPublic(string $name, UploadedFile|File $file, string $type)
    {
        return Storage::disk('public')->putFileAs($type, $file, $name);
    }

    public function getAbsolutePathOf(string $name, string $type, bool $isPrivate, string $diskName = 'private')
    {
        return $this->disk($isPrivate, $diskName)->path($this->directoryPrefix($type, $name));
    }

    public function isFileExists(string $name, string $type, bool $isPrivate, string $diskName = 'private')
    {
        return $this->disk($isPrivate, $diskName)->exists($this->directoryPrefix($type, $name));
    }

    public function getFile(string $name, string $type, bool $isPrivate, string $diskName = 'private')
    {
        return $this->disk($isPrivate, $diskName)->download($this->directoryPrefix($type, $name));
    }

    public function deleteFile(string $name, string $type, bool $isPrivate, string $diskName = 'private')
    {
        return $this->disk($isPrivate, $diskName)->delete($this->directoryPrefix($type, $name));
    }

    private function directoryPrefix($type, $name)
    {
        return $type . DIRECTORY_SEPARATOR . $name;
    }

    private function disk(bool $isPrivate, string $diskName)
    {
        return $isPrivate ? Storage::disk($diskName) : Storage::disk('public');
    }
}
