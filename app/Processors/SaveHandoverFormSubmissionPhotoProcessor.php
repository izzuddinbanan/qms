<?php

namespace App\Processors;

class SaveHandoverFormSubmissionPhotoProcessor extends ImageProcessor
{
    /**
     * execute command handler
     * @return void
     */
    public function execute()
    {
        $path = public_path('uploads/photo-submissions');

        $image = $this->processImage($path);

        return $url = ['name' => $image['name_unique'], 'url'  => url('uploads/photo-submissions') . '/' . $image['name_unique']];
    }
}
