<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CarPhotoService
{
    /**
     * Maximum width for the stored photo.
     */
    private const MAX_WIDTH = 1600;

    /**
     * Maximum width for the generated thumbnail.
     */
    private const THUMBNAIL_WIDTH = 480;

    /**
     * Store an uploaded photo as compressed WebP with a thumbnail.
     */
    public function store(UploadedFile $file, string $directory = 'cars'): string
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagewebp')) {
            return $file->store($directory, 'public');
        }

        $source = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if (! $source instanceof \GdImage) {
            return $file->store($directory, 'public');
        }

        $name = Str::uuid()->toString();
        $path = "{$directory}/{$name}.webp";
        $thumbnail = $this->thumbnailPath($path);

        $main = $this->resize($source, self::MAX_WIDTH);
        $thumb = $this->resize($source, self::THUMBNAIL_WIDTH);

        Storage::disk('public')->put($path, $this->encode($main));
        Storage::disk('public')->put($thumbnail, $this->encode($thumb));

        imagedestroy($source);
        imagedestroy($main);
        imagedestroy($thumb);

        return $path;
    }

    /**
     * Delete a stored photo and its thumbnail.
     */
    public function delete(?string $path): void
    {
        if (blank($path)) {
            return;
        }

        $disk = Storage::disk('public');
        $disk->delete($path);
        $disk->delete($this->thumbnailPath($path));
    }

    /**
     * Get the thumbnail path for a stored photo.
     */
    public function thumbnailPath(string $path): string
    {
        $directory = trim(dirname($path), '.');

        return ($directory === '' ? '' : $directory.'/').'thumbs/'.basename($path);
    }

    /**
     * Resize an image to the given maximum width.
     */
    private function resize(\GdImage $source, int $maxWidth): \GdImage
    {
        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, $maxWidth / max(1, $width));
        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefilledrectangle($canvas, 0, 0, $newWidth, $newHeight, $transparent);
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return $canvas;
    }

    /**
     * Encode a GD image as WebP.
     */
    private function encode(\GdImage $image): string
    {
        ob_start();
        imagewebp($image, null, 82);

        return (string) ob_get_clean();
    }
}
