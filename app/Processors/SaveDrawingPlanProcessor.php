<?php

namespace App\Processors;

class SaveDrawingPlanProcessor extends ImageProcessor
{
    /**
     * execute command handler
     * @return void
     */
    public function execute()
    {    
        $path = public_path('uploads/drawings');
        $thumbnail_path = public_path('uploads/drawings/thumbnail');

        $image = $this->processImage($path, 400, null, $thumbnail_path);
        
        return $image;
    }
}
