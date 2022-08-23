<?php
namespace App\Http\Controllers\Ajax;

use File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use App\Entity\Form;

class UploadFormController extends Controller
{

    /**
     *
     * @param Request $request
     */
    public function upload(Request $request)
    {
        $path = public_path(Form::FILE_PATH);
        
        $image = \App\Processors\SaveFormProcessor::make($request->file('file'))->execute($path);
        
        return Response::json($image);
    }

    /**
     *
     * @param Request $request
     */
    public function destroy(Request $request)
    {
        if ($request->ajax()) {
            
            File::delete(public_path('uploads/forms') . DIRECTORY_SEPARATOR . '' . $request->input('image'));
            
            return Response::json([
                'status' => 'ok'
            ]);
        }
        return Response::json([
            'status' => 'fail'
        ]);
    }
}
