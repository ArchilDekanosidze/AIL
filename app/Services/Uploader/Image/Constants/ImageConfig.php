<?php
namespace App\Services\Uploader\Image\Constants;

class ImageConfig
{
    public static function driver(): string
    {
        return 'gd';
    }

    public static function indexSizes(): array
    {
        return [
            'large' => ['width' => 800, 'height' => 600],
            'medium' => ['width' => 400, 'height' => 300],
            'small' => ['width' => 80, 'height' => 60],
        ];
    }

    public static function defaultIndexSize(): string
    {
        return 'medium';
    }

    public static function cacheSizes(): array
    {
        return [
            'large' => ['width' => 800, 'height' => 600],
            'medium' => ['width' => 400, 'height' => 300],
            'small' => ['width' => 80, 'height' => 60],
        ];
    }

    public static function defaultCacheSize(): string
    {
        return 'medium';
    }

    public static function cacheLifetime(): int
    {
        return 10; // minutes
    }

    public static function imageNotFound(): string
    {
        return ''; // path to placeholder image
    }

    public static function getSize(string $type = 'index', ?string $size = null): array
    {
        if ($type === 'cache') {
            $sizes = self::cacheSizes();
            $size = $size ?? self::defaultCacheSize();
        } else {
            $sizes = self::indexSizes();
            $size = $size ?? self::defaultIndexSize();
        }

        return $sizes[$size] ?? ['width' => 100, 'height' => 100];
    }
}
