<?php

namespace App\Processors;

class SaveSignatureProcessor extends ImageProcessor
{
    /**
     * execute command handler
     * @return void
     */
    public function execute()
    {
        $path = public_path('uploads/signatures');
        $thumbnail_path = public_path('uploads/signatures/thumbnail');

        $image = $this->processImage($path);

        return $image['name_unique'];
    }
}
