<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{

    /**
     * @var string
     */
    public static $wrap = 'Data';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    // public function toArray($request)
    // {
    //     return parent::toArray($request);
    // }

    /**
     * @param $request
     */
    public function with($request)
    {
        return [
            'AppData' => $this->appData,
        ];
    }
}
