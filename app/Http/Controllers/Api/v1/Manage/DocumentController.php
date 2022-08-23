<?php

namespace App\Http\Controllers\Api\v1\Manage;

use App\Entity\DrawingPlan;
use App\Entity\DrawingSet;
use App\Entity\IssueFormSubmission;
use App\Entity\Project;
use App\Http\Controllers\Api\v1\BaseApiController;
use App\Http\Controllers\Traits\ReturnErrorMessage;
use App\Http\Resources\BaseResource;
use App\Http\Resources\DocumentCollection;
use App\Supports\AppData;
use Illuminate\Http\Request;


class DocumentController extends BaseApiController
{

    use AppData, ReturnErrorMessage;

    public function __construct()
    {
        parent::__construct();
    }


 	/**
     * @SWG\Post(
     *     path="/document",
     *     summary="List all document",
     *     method="post",
     *     tags={"Document"},
     *     description="Use this API to retrieve list of all document.",
     *     operationId="listDocument",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="os",type="string",example="AND:0000"),
     *                      @SWG\Property(property="project_id",type="string",example="1"),
     *
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     * @param $string
     */
    public function listDocument(Request $request)
    {
        $user = $this->user;
        $data = $this->data;

        $project = Project::find($request->input('data.project_id'));

        $projectDoc = $project->document()->with('activeVersion')->get();

        $drawingset = DrawingSet::where('project_id', $project->id)->select('id')->get();
        $drawingPlan = DrawingPlan::whereIn('drawing_set_id', $drawingset)->select('id')->get();

        $form_submission = IssueFormSubmission::whereIn('drawing_plan_id', $drawingPlan)->get();

        $data_arr = [];
        foreach ($projectDoc as $key => $value) {

            $data_arr[] = (object) [
                'id'    => $value->id,
                'drawing_plan_id'  => '',
                'name'  => $value->name,
                'file'  => $value->activeVersion->file,
                'url'   => $value->activeVersion->url
            ];
        }

        foreach ($form_submission as $key => $value) {
            if($value->pdf_name){
                $data_arr[] = (object) [
                    'id'                => $value->id,
                    'drawing_plan_id'   => $value->drawing_plan_id,
                    'name'              => $value->submission_type == 'OA Sign Off' ? 'OA Sign Off' : 'Close And Handover',
                    'file'              => $value->pdf_name  ?? '',
                    'url'               => $value->pdf_name ? url('uploads/oasignoff-form-submission/'. $value->pdf_name) : '',
                    'datetime'          => $value->created_at ? $value->created_at->toDateTimeString() : '',
                ];
            }
        }

        return ['Data' => $data_arr, 'AppData' => $this->prepareAppData($request, $data)];

        // array_push($data, $projectDoc);

        // $projectDoc->appData = $this->prepareAppData($request, $data);

        // return (new DocumentCollection($projectDoc))->additional(['AppData' => $this->appData]);

    }
    
}
