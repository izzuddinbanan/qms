<?php

namespace App\Http\Controllers\Api\v1\Manage;

use App\Entity\Language;
use App\Supports\AppData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\v1\BaseApiController;
use App\Http\Resources\LanguageCollection;
use App\Http\Resources\ListOptionResource;


class OptionController extends BaseApiController
{

    use AppData;

    /**
     * @SWG\Post(
     *     path="/option/general",
     *     summary="get general option select such as list of language",
     *     method="POST",
     *     tags={"Option"},
     *     description="Use this API to retrieve list of option dropdown.",
     *     operationId="general",
     *     produces={"application/json"},
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     * @param $string
     */
    public function general(Request $request)
    {

    	$user = $this->user;
        $data = $this->data;

    	$language = Language::get();

        $langCollection = (new LanguageCollection($language));


        $option =  (object) ['lang'       => $langCollection];


        array_push($data, $option);

        $option->appData = $this->prepareAppData($request, $data);

        return new ListOptionResource($option);
    }
}
