<?php

namespace App\Processors;

use File;
use Image;

class ImageProcessor
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * static call method
     * @return static
     */
    public static function make($data)
    {
        return new static($data);
    }

    public function processImage($file_path, $width = null, $height = null, $file_thumbnail_path = null)
    {
        if (!File::isDirectory($file_path)) {
            File::makeDirectory($file_path, 0777, true);
        }

        $File = $this->data;
        $origin_name = $File->getClientOriginalName();
        $origin_extension = $File->getClientOriginalExtension();
        $image_name = (str_replace(array('.', ' '), '', microtime(true))) . '.' . $origin_extension;

        // original image
        $original_image = Image::make($File)->save($file_path . '/' . $image_name);
        chmod($file_path . '/' . $image_name, 0777);

        if ($file_thumbnail_path) {
            if (!File::isDirectory($file_thumbnail_path)) {
                File::makeDirectory($file_thumbnail_path, 0777, true);
            }

            Image::make($File)->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('jpg', 0)->save($file_thumbnail_path . '/' . $image_name);
        }

        return [
            'image_name' => $origin_name,
            'name_unique' => $image_name,
            'width' => $original_image->width(),
            'height' => $original_image->height()
        ]; 
    }
}
