<?php

namespace App\Processors;

use File;

class DeleteFileProcessor
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
        $this->data = $data;    //location file
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

        File::delete($this->data);
    }
}
