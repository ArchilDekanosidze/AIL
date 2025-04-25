<?php
namespace App\Services\Uploader\Image;

use App\Services\Uploader\Image\Constants\ImageConfig;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Config;
use App\Services\Uploader\StorageManagerService;
use App\Services\Uploader\Image\Constants\ImageServiceInterface;
use Intervention\Image\Drivers\Gd\Driver as GdDriver; // Or use Imagick if needed
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\WebpEncoder;

class ImageInterventionService implements ImageServiceInterface
{
    private $storageManager;
    private $manager;
    private $keepAspectRatio = false;
    public function __construct(ImageStorageManager $storageManager)
    {
        $this->storageManager = $storageManager;
        $this->manager = new ImageManager(new GdDriver());

    }

    public function save($image)
    {
        $encoder = $this->prepareStorage($image);        
        $convertedImage = $this->manager->read($image->getRealPath())->encode($encoder);
        return $this->storeAndGetAddress($convertedImage);
    }


    public function fitAndSave($image, $width, $height)
    {
        $encoder = $this->prepareStorage($image);
        if($this->keepAspectRatio)
        {
            $convertedImage = $this->manager
            ->read($image->getRealPath())
            ->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();  // Optional: to prevent upsizing the image
            })
            ->encode($encoder);
        }
        else
        {
            $convertedImage = $this->manager->read($image->getRealPath())->cover($width, $height)->encode($encoder);
        }

        return $this->storeAndGetAddress($convertedImage);
    }

    public function createIndexAndSave($image)
    {
        $imageSizes = ImageConfig::indexSizes();

        $encoder = $this->prepareStorage($image);

        $imageName = $this->storageManager->getFileName();


        $indexArray = [];
        foreach ($imageSizes as $sizeAlias => $imageSize) {

            $currentImageName = $imageName . '_' . $sizeAlias;
            $this->storageManager->setFileName($currentImageName);

            $this->storageManager->provider();
            if($this->keepAspectRatio)
            {
                $convertedImage = $this->manager
                ->read($image->getRealPath())
                ->resize($imageSize['width'], $imageSize['height'], function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();  // Optional: to prevent upsizing the image
                })
                ->encode($encoder);
            }
            else
            {
                $convertedImage = $this->manager->read($image->getRealPath())->cover($imageSize['width'], $imageSize['height'])->encode($encoder);

            }


            $indexArray[$sizeAlias] = $this->storeAndGetAddress($convertedImage);
        }
        $images['indexArray'] = $indexArray;
        $images['currentImage'] = ImageConfig::defaultIndexSize();

        return $images;
    }

    public function deleteFiles($indexArray)
    {
        foreach ($indexArray as $file) {
            $this->storageManager->deleteFile($file);
        }
        $this->storageManager->deleteDirectory(dirname($file));
    }

    public function setExclusiveDirectory($exclusiveDirectory)
    {
        $this->storageManager->setExclusiveDirectory($exclusiveDirectory);
    }

    public function setDisk($disk)
    {
        $this->storageManager->setDisk($disk);
    }

    public function prepareStorage($image)
    {
        $this->storageManager->setFile($image);
        $fileFormat  = $this->storageManager->getFileFormat();
        $encoder = match (strtolower($fileFormat)) {
            'jpeg', 'jpg' => new JpegEncoder(quality: 85),
            'png'         => new PngEncoder(),
            'webp'        => new WebpEncoder(),
            default       => throw new \InvalidArgumentException("Unsupported file format: $fileFormat"),
        };
        return $encoder;
    }

    public function storeAndGetAddress($convertedImage)
    {
        $this->storageManager->setConvertedFile($convertedImage);
        $this->storageManager->putFile();
        return $this->storageManager->getFileAddress();
    }

    public function setKeepAspectRatio()
    {
        $this->keepAspectRatio = true;
    }
}
