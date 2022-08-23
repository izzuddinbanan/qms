<?php

namespace App\Http\Controllers\Ajax;

use File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Intervention\Image\Facades\Image;

class UploadImageController extends Controller
{
    /**
     * @param Request $request
     */
    public function upload(Request $request)
    {

        $image = \App\Processors\SaveDrawingPlanProcessor::make($request->file('file'))->execute();

        if($request->input('type') != "custom" ){
            $name = explode('_', $image["image_name"]);

            if(count($name) < 4 || count($name) > 4){
                return \Response::json(array("wrong format name"), 500);
            }
        }
        // if($name[1] == "unit" || $name[1] == "common"){
            return Response::json($image);
        // }
    }

    /**
     * @param Request $request
     */
    public function destroy(Request $request)
    {
        if ($request->ajax()) {
            
            File::delete(public_path('uploads/drawings') . DIRECTORY_SEPARATOR . '' . $request->input('image'));
            File::delete(public_path('uploads/drawings/thumbnail') . DIRECTORY_SEPARATOR . '' . $request->input('image'));

            return Response::json(['status' => 'ok']);

        }
        return Response::json(['status' => 'fail']);
    }


    public function uploadDefect(Request $request)
    {

        $image = \App\Processors\SaveIssueProcessor::make($request->file('file'))->execute();

        return Response::json($image);
    }

    /**
     * @param Request $request
     */
    public function destroyDefect(Request $request)
    {
        if ($request->ajax()) {

            File::delete(public_path('uploads/issues') . DIRECTORY_SEPARATOR . '' . $request->input('image'));
            File::delete(public_path('uploads/issues/thumbnail') . DIRECTORY_SEPARATOR . '' . $request->input('image'));

            return Response::json(['status' => 'ok']);

        }
        return Response::json(['status' => 'fail']);
    }
}
