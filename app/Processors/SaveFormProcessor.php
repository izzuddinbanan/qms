<?php
namespace App\Processors;

use File;
use Intervention\Image\Facades\Image;

class SaveFormProcessor
{

    /**
     *
     * @var mixed
     */
    protected $data;

    /**
     *
     * @param
     *            $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * static call method
     *
     * @return static
     */
    public static function make($data)
    {
        return new static($data);
    }

    /**
     * execute command handler
     *
     * @return void
     */
    public function execute($path)
    {
        $name_unique = time() . rand(10, 99) . '.png';
        // $image_name = str_replace(' ', '_', substr($this->data->getClientOriginalName(),0,-4));
        $image_name = substr($this->data->getClientOriginalName(), 0, - 4);
        
        if (! File::isDirectory($path)) {
            
            File::makeDirectory($path, 0775, true);
        }
        
        $image = Image::make($this->data);
        
        $size['width'] = $image->width();
        $size['height'] = $image->height();
        
        $image->save($path . DIRECTORY_SEPARATOR . '' . $name_unique);
        
        return [
            'image_name' => $image_name,
            'name_unique' => $name_unique,
            'width' => $size['width'],
            'height' => $size['height']
        ];
    }
}
