<?php
namespace App\Services\Uploader\Image\Constants;

use App\Services\Uploader\Image\ImageStorageManager;


interface ImageServiceInterface
{
    public function __construct(ImageStorageManager $storageManager);
    public function save($image);
    public function fitAndSave($image, $width, $height);

    public function createIndexAndSave($image);

    public function deleteFiles($indexArray);

    public function setExclusiveDirectory($exclusiveDirectory);
    public function setDisk($disk);
}
