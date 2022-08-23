<?php

namespace App\Processors;

use File;

class SaveFileProcessor
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

        $file_name = time() . rand(10, 99) . '.' . $this->data->getClientOriginalExtension();

        $path = public_path('uploads/documents');

        if (!File::isDirectory($path)) {

            File::makeDirectory($path, 0775, true);
        }
        $this->data->move($path,$file_name);

        return $file_name;
    }
}
