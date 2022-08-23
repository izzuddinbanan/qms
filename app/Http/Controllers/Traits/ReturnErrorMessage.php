<?php

namespace App\Http\Controllers\Traits;
use App\Http\Resources\BaseResource;

trait ReturnErrorMessage
{
    
    public function failData($request, $data, $message = ""){

        $status = $this->failedAppData($message);

        $emptyData = collect();
        $emptyData->appData = $this->prepareAppData($request, $data, $status);

        return new BaseResource($emptyData);
    }

    public function failValidation($request, $validator, $data){

        $status = $this->failedAppData($validator->errors()->first());

        $emptyData = collect();
        $emptyData->appData = $this->prepareAppData($request, $data, $status);

        return new BaseResource($emptyData);
    }

    public function emptyData($request, $data){

        $emptyData = collect();
        $emptyData->appData = $this->prepareAppData($request, $data);

        return new BaseResource($emptyData);

    }
}
