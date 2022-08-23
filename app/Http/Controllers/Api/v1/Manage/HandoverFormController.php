<?php

namespace App\Http\Controllers\Api\v1\Manage;

use App\Entity\DrawingPlan;
use App\Entity\DrawingSet;
use App\Entity\HandOverFormAcceptance;
use App\Entity\HandOverFormList;
use App\Entity\HandOverMenu;
use App\Entity\HandoverFormSubmission;
use App\Entity\HandoverFormSurvey;
use App\Entity\HandoverFormSurveyVersion;
use App\Entity\Project;
use App\Http\Controllers\Api\v1\BaseApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ReturnErrorMessage;
use App\Http\Resources\BaseResource;
use App\Http\Resources\HandoverFormCollection;
use App\Http\Resources\HandoverSubmittedCollection;
use App\Supports\AppData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class HandoverFormController extends BaseApiController
{
    use AppData, ReturnErrorMessage;
    
    /**
     * @SWG\Post(
     *     path="/handOverForm",
     *     summary="To view handover settings",
     *     method="post",
     *     tags={"Handover Form"},
     *     description="This Api will show handover settings.",
     *     operationId="getHandoverDetails",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="drawing_plan_id",type="string",example="1"),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function getHandOverDetails(Request $request)
    {
        //ask for user who use this form

        $data = $this->data;
        $user = $this->user;

        $rules = [
            'drawing_plan_id'       => 'required',
        ];

        $message = [
            'drawing_plan_id.required'         => "Drawing Plan ID is required.",
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);
        // return $user->current_role;exit();
        
        if($user->current_role != 8)
        {
            $status = $this->failedAppData("Unauthorized access.");

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if ($validator->fails()) {
            return $this->failData($request, $data, $validator->errors()->first());
        }

        $drawing_plan = DrawingPlan::where('id', $request->input('data')['drawing_plan_id'])->first();
        $menu = $drawing_plan->drawingSet->project->handOVerMenuActive;

        foreach ($menu as $key => $value) {
            $menu[$key]['drawing_plan_id'] =  $drawing_plan->id;
        }

        array_push($data, $menu);

        $menu->appData = $this->prepareAppData($request, $data);

        return (new HandoverFormCollection($menu))->additional(['AppData' => $this->appData]);
    }

    /**
     * @SWG\Post(
     *     path="/handOverForm/submit",
     *     summary="To submit handover form",
     *     method="post",
     *     tags={"Handover Form"},
     *     description="This Api will submit handover form.",
     *     operationId="handoverFormSubmit",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="drawing_plan_id",type="string",example="1"),
     *                      @SWG\Property(property="key",type="string",example="[]"),
     *                      @SWG\Property(property="es",type="string",example="[]"),
     *                      @SWG\Property(property="waiver",type="string",example="[]"),
     *                      @SWG\Property(property="photo",type="string",example="[]"),
     *                      @SWG\Property(property="acceptance",type="string",example="[]"),
     *                      @SWG\Property(property="survey",type="string",example="[]"),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function formSubmit(Request $request)
    {
        $data = $this->data;
        $user = $this->user;

        $rules = [
            'drawing_plan_id'       => 'required',
        ];

        $message = [
            'drawing_plan_id.required'         => "Drawing Plan ID is required.",
            'key.form_id.required'             => "Key form ID is required.",
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);

        if ($validator->fails()) {
            return $this->failData($request, $data, $validator->errors()->first());
        }

        if($user->current_role != 8)
        {
            $status = $this->failedAppData("Unauthorized access.");

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        $drawing_plan = DrawingPlan::where('id', $request->input('data')['drawing_plan_id'])->first();
        $project = $drawing_plan->drawingSet->project;
        
        $menu = $project->handOVerMenuActive;

        foreach ($menu as $key => $value) {
            if($value->original_name == 'photo'){
                if((!$request->hasFile('data.photo')) && $value->field_mandatory == 'yes'){
                    return $this->failData($request, $data, "$value->original_name form not found.".$value);
                }
            }else{
                 if(!isset($request->input('data')[$value->original_name]) && $value->field_mandatory == 'yes'){
                    return $this->failData($request, $data, "$value->original_name form not found.");
                }
            }
        }

        $survey = []; 
        $acceptance = [];
        $waiver = [];
        $photo = [];
        $es = [];
        $key = [];

        foreach ($menu as $key => $value) {

            switch ($value->original_name) {
                case 'survey':
                    $survey = $this->formSubmissionSurvey($request->input('data.survey'), $value->field_mandatory, $request, $data, $project);
                    if(isset($survey->status) && $survey->status == 'failed'){
                        return $this->failData($request, $data, $survey->message);
                    }

                    break;
                case 'acceptance':
                    $acceptance = $this->formSubmissionAcceptance($request->input('data.acceptance'), $value->field_mandatory, $request, $data, $project);
                    if(isset($acceptance->status) && $acceptance->status == 'failed'){
                        return $this->failData($request, $data, $acceptance->message);
                    }
                    break;
                case 'waiver':
                    $waiver = $this->formSubmissionWaiver($value->field_mandatory, $request, $data);
                    if(isset($waiver->status) && $waiver->status == 'failed'){
                        return $this->failData($request, $data, $waiver->message);
                    }
                    break;
                case 'photo':
                    $photo = $this->formSubmissionPhoto($request);
                    if(isset($photo->status) && $photo->status == 'failed'){
                        return $this->failData($request, $data, $photo->message);
                    }
                    break;
                case 'es':
                    $es = $this->formSubmissionEs($request->input('data.es'), $value->field_mandatory, $request, $data);
                    if(isset($es->status) && $es->status == 'failed'){
                        return $this->failData($request, $data, $es->message);
                    }
                    break;
                case 'key':
                    $keySubmission = $this->formSubmissionKey($request->input('data.key'), $value->field_mandatory, $request, $data);
                    if(isset($key->status) && $key->status == 'failed'){
                        return $this->failData($request, $data, $key->message);
                    }
                    break;
            }

        }

        $form = HandoverFormSubmission::create([
            'drawing_plan_id'       => $drawing_plan->id ?? '',
            'key_submission'        => $keySubmission ?? '',
            'es_submission'         => $es ?? '',
            'waiver_submission'     => $waiver ?? '',
            'photo_submission'      => $photo ?? '',
            'acceptance_submission' => $acceptance ?? '',
            'survey_submission'     => $survey ?? '',
            'survey_form_id'        => $survey->id ?? '',
            // 'pdf_name'              => null,
            'created_by'            => $user->id ?? '',
        ]);

        $pdf = $this->generatePdf($form);

        $form->forceFill(['pdf_name'    => $pdf])->save();

        // $drawing_plan = DrawingPlan::where('id', $request->input('data')['drawing_plan_id'])->first();

        $drawing_plan->update([
            'handover_status'   => 'handed over',
        ]);

        $emptyData = collect();
        $emptyData->appData = $this->prepareAppData($request, $data);
        return new BaseResource($emptyData);
    }

    /**
     * @SWG\Post(
     *     path="/handOverForm/handOverChecklist",
     *     summary="To view handover checklist history",
     *     method="post",
     *     tags={"Handover Form"},
     *     description="This Api will show handover settings.",
     *     operationId="getHandoverChecklist",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="project_id",type="string",example="1"),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function handOverChecklist(Request $request)
    {
        $data = $this->data;
        $user = $this->user;

        $rules = [
            'project_id'       => 'required',
        ];

        $message = [
            'project_id.required'         => "Project ID is required.",
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);

        if ($validator->fails()) {
            return $this->failData($request, $data, $validator->errors()->first());
        }

        if($user->current_role != 8)
        {
            $status = $this->failedAppData("Unauthorized access.");

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        $drawingSet = DrawingSet::where('project_id', $request->input('data.project_id'))->select('id')->get();

        $drawingPlan = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->select('id')->get(); 

        $handOverChecklist = HandoverFormSubmission::whereIn('drawing_plan_id', $drawingPlan)->get();
        
        if(count($handOverChecklist)>0)
        {
            array_push($data, $handOverChecklist);

            $handOverChecklist->appData = $this->prepareAppData($request, $data);

            return (new HandoverSubmittedCollection($handOverChecklist))->additional(['AppData' => $this->appData]);
        }
        else{
            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data);
            return new BaseResource($emptyData);
        }
    }

    public function formSubmissionKey($input, $field_mandatory, $request, $data){

        if(!$form = HandOverFormList::find($input['form_id'] ?? '')){
            return (object)['status' => "failed" , 'message' => 'Form not found'];
        }

        foreach ($form->section as $keySection => $valSection) {

            $item_arr = [];
            foreach ($valSection->item as $keyItem => $valItem) {
                    

                if((!isset($input["item"][$valItem->id]['value']) || !$input["item"][$valItem->id]['value']) &&  $field_mandatory == 'yes'){
                    return (object)['status' => "failed" , 'message' => 'All field in key form is required.'];
                }

                $item_arr[] = (object) [
                    'id'          => $valItem->id,
                    'name'        => $valItem->name,
                    'quantity'    => $valItem->quantity,
                    'value'       => $input["item"][$valItem->id]["value"] ?? '',
                    'remarks'     => $input["item"][$valItem->id]["remarks"] ?? '',
                ];

            };

            $secion_arr[] = (object)[
                'id'    => $valSection->id,
                'name'  => $valSection->name,
                'item'  => $item_arr,
            ];

        }


        return $form_arr = (object)[
            'id'             => $form->id,
            'name'           => $form->name,
            'meter_reading'  => $form->meter_reading,
            'description'    => $form->description,
            'section'        => $secion_arr,
        ];
    }

    public function formSubmissionEs($input, $field_mandatory, $request, $data) {

        if(!$form = HandOverFormList::find($input['form_id'] ?? '')){
            return (object)['status' => "failed" , 'message' => 'Form not found'];
        }

        foreach ($form->section as $keySection => $valSection) {

            $item_arr = [];
            foreach ($valSection->item as $keyItem => $valItem) {
                    

                if((!isset($input["item"][$valItem->id]['value']) || !$input["item"][$valItem->id]['value']) &&  $field_mandatory == 'yes'){
                    return (object)['status' => "failed" , 'message' => "All field in es form is required."];
                }


                $item_arr[] = (object) [
                    'id'          => $valItem->id,
                    'name'        => $valItem->name,
                    'quantity'    => $valItem->quantity,
                    'value'       => $input["item"][$valItem->id]["value"] ?? '',
                    'remarks'     => $input["item"][$valItem->id]["remarks"] ?? '',
                ];

            };

            $secion_arr[] = (object)[
                'id'    => $valSection->id,
                'name'  => $valSection->name,
                'item'  => $item_arr,
            ];

        }

        $drawing_plan = DrawingPlan::where('id', $request->input('data')['drawing_plan_id'])->first();
        $unit_owner = (object) [
            'car_park'      => $drawing_plan->car_park,
            'access_card'   => $drawing_plan->access_card,
            'key_fob'       => $drawing_plan->key_fob,
        ];



        $meter_read = (object) [
            'electricity'      => $input['electric'] ?? '',
            'water'   => $input['water'] ?? '',
            'date'    => now(),
        ];


        return $form_arr = (object)[
            'id'             => $form->id,
            'name'           => $form->name,
            'meter_reading'  => $form->meter_reading,
            'description'    => $form->description,
            'section'        => $secion_arr,
            'unit_owner'     => $unit_owner,
            'meter_read'     => $meter_read,
        ];
    }

    public function formSubmissionSurvey($input, $field_mandatory, $request, $data, $project)
    {
        // survey
        $survey = $input;
        //validate mandatory survey
        if($field_mandatory == "yes")
        {
            $survey_question_version = HandoverFormSurveyVersion::where('project_id', $project->id)->where('id', $survey['form_id'])->first();
            $survey_question = HandoverFormSurvey::where('project_id', $project->id)->where('handover_form_survey_id', $survey_question_version->id)->get();

            foreach ($survey_question as $surveyItem => $surveyValue) {

                // if(!isset($survey[$surveyValue->id]['value']) && $survey[$surveyValue->id]['value'] != "" &&  $field_mandatory == 'yes'){

                //     return (object)['status' => "failed" , 'message' => "All field in survey form is required."];

                // }

                $survey_arr[] = (object) [
                    'id'            => $surveyValue->id,
                    'question'      => $surveyValue->question,
                    'sequence'      => $surveyValue->sequence,
                    'value'         => $survey[$surveyValue->id]['value'] ?? '',
                    'type'          => $surveyValue->type,
                    'project_id'    => $surveyValue->project_id,
                ];

                $survey_collection = (object) [
                    'id'        => $survey_question_version->id,
                    'version'   => $survey_question_version->version,
                    'value'     => $survey_arr,
                ];

            }
            
            return $survey_collection;
        }
    }

    public function formSubmissionAcceptance($input, $field_mandatory, $request, $data, $project)
    {
        $acceptance = $input;

        if($field_mandatory == "yes")
        {
            $acceptance_required_field = ['form_id', 'remarks', 'received_by_name', 'received_by_ic_passport', 'received_by_datetime', 'attended_by_name', 'attended_by_datetime', 'attended_by_designation'];

            foreach($acceptance_required_field as $arf)
            {
                if(isset($acceptance[$arf]) && !$acceptance[$arf])
                {
                    return (object)['status' => "failed" , 'message' => "All field in acceptance form is required."];
                }
            }

            if(!isset($request->file('data')['acceptance']['received_by_signature']) && !$request->hasFile('data.acceptance.received_by_signature') && !isset($request->file('data')['acceptance']['attended_by_signature']) && !$request->hasFile('data.acceptance.attended_by_signature'))
            {
                return (object)['status' => "failed" , 'message' => "Signature is not provided."];
            }    
        }
        
        $acceptance_collection = (object)[
            'form_id'                   => $acceptance['form_id'] ?? '', 
            'remarks'                   => $acceptance['remarks'] ?? '',
            'received_by_name'          => $acceptance['received_by_name'] ?? '',
            'received_by_ic_passport'   => $acceptance['received_by_ic_passport'] ?? '',
            'received_by_signature'     => $request->hasFile('data.acceptance.received_by_signature') ? \App\Processors\SaveSignatureProcessor::make($request->File('data.acceptance.received_by_signature'))->execute() : '',
            'received_by_datetime'      => $acceptance['received_by_datetime'] ?? '',
            'attended_by_name'          => $acceptance['attended_by_name'] ?? '',
            'attended_by_signature'     => $request->hasFile('data.acceptance.attended_by_signature') ? \App\Processors\SaveSignatureProcessor::make($request->File('data.acceptance.attended_by_signature'))->execute() : '',
            'attended_by_designation'   => $acceptance['attended_by_designation'] ?? '',
            'attended_by_datetime'      => $acceptance['attended_by_datetime'] ?? '',
        ];

        return $acceptance_collection;
    }

    public function formSubmissionWaiver($field_mandatory, $request, $data) {

        if( (!$request->hasFile('data.waiver.signature') || !$request->input('data.waiver.name') || !$request->input('data.waiver.datetime') ) &&  $field_mandatory == 'yes'){

            return (object)['status' => "failed" , 'message' => "All field in waiver form is required."];
        }

        return $waiver = (object) [
            'signature'     => $request->hasFile('data.waiver.signature') ? \App\Processors\SaveSignatureProcessor::make($request->File('data.waiver.signature'))->execute() : '',
            'name'          => $request->input('data.waiver.name') ?? '',
            'created_at'    => $request->input('data.waiver.datetime') ?? '',
        ];
    }

    public function formSubmissionPhoto($request) {
        
        $photo = [];
        if($request->file('data.photo'))
        {
            foreach ($request->file('data.photo') as $key => $value) {
                $photo[] = (object)\App\Processors\SaveHandoverFormSubmissionPhotoProcessor::make($value)->execute();
            };    
        }
        

        return $photo;


    }

    public function generatePdf($form){

        $project = $form->drawingPlan->drawingSet->project;
        $unit = $form->drawingPlan;
        $user = $form->drawingPlan->unitOwner;

        $waiver = $project->HandoverFormWaiver;

        $acceptance = HandOverFormAcceptance::find($form->acceptance_submission["form_id"]);

        $path = public_path('uploads/handover-form-submission');

        if (!\File::isDirectory($path)) {

            \File::makeDirectory($path, 0775, true);
        }

        $unique_name = time() . rand(10, 9999) . '.pdf';

        $pdf = \PDF::loadView('pdf.handover-form-submissions.index', compact('project', 'unit', 'user', 'form', 'waiver', 'acceptance'));
        $pdf->save("{$path}/${unique_name}");

        return $unique_name;

    }
}



