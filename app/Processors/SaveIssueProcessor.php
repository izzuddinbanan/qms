<?php

namespace App\Processors;

class SaveIssueProcessor extends ImageProcessor
{
    /**
     * execute command handler
     * @return void
     */
    public function execute()
    {
        $path = public_path('uploads/issues');
        $thumbnail_path = public_path('uploads/issues/thumbnail');

        $image = $this->processImage($path, 200, null, $thumbnail_path);

        return $image['name_unique'];
    }
}
