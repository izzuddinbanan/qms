<?php

namespace App\Processors;

use File;
use Intervention\Image\Facades\Image;

class SaveLogoClientProcesscor
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

    /**
     * execute command handler
     * @return void
     */
    public function execute()
    {
        $image_name = time() . rand(10, 99) . '.png';

        $path = public_path('uploads/client_logo/');

        if (!File::isDirectory($path)) {

            File::makeDirectory($path, 0775, true);
        }

        Image::make($this->data)->save($path . DIRECTORY_SEPARATOR . $image_name);
        return $image_name;
    }
}
