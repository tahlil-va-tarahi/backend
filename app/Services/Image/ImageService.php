<?php

namespace App\Services\Image;

use Illuminate\Support\Facades\Config;
use Intervention\Image\Facades\Image;

class ImageService extends ImageToolsService
{
    public function moveToStorage($image)
    {
        $this->setImage($image);
        $this->provider();

        $result = Image::make($image->getRealPath())
            ->save(storage_path($this->getImageAddress()), null, $this->getImageFormat());
        return $result ? $this->getImageAddress() : false;
    }


    public function moveToPublic($image, $width, $height)
    {
        $this->setImage($image);
        $this->provider();

        $result = Image::make($image->getRealPath())
            //->fit($width, $height)
            ->save(public_path($this->getImageAddress()), 7, $this->getImageFormat());

        return $result ? $this->getImageAddress() : false;
    }

    public function deleteImage($imagePath)
    {
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    public function deleteDirectoryAndFiles($directory)
    {
        if (!is_dir($directory)) {
            return false;
        }

        $files = glob($directory . DIRECTORY_SEPARATOR . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->deleteDirectoryAndFiles($file);
            }
            else {
                unlink($file);
            }
        }
        return rmdir($directory);
    }


}
