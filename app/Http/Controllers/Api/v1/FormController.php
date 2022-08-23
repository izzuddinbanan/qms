<?php
namespace App\Http\Controllers\Api\v1;

use File;
use DB;
use Validator;
use Carbon\Carbon;
use App\Entity\Attribute;
use App\Entity\Issue;
use App\Entity\SubmissionHistory;
use App\Entity\FormGroup;
use App\Entity\FormGroupStatus;
use App\Supports\AppData;
use App\Entity\Project;
use App\Entity\Submission;
use App\Entity\FormVersion;
use App\Entity\LocationPoint;
use Illuminate\Http\Request;
use App\Entity\GeneralStatus;
use App\Entity\ProjectDataStatus;
use App\Entity\SubmissionFormGroup;
use App\Http\Resources\BaseResource;
use App\Http\Resources\AllFormCollection;
use App\Http\Resources\FormCollection;
use App\Entity\FormAttributeLocation;
use Intervention\Image\Facades\Image;
use App\Http\Resources\SubmissionResource;
use App\Http\Resources\SubmissionCollection;
use App\Http\Resources\FormSubmissionColllection;
use App\Http\Resources\FormSubmissionListResource;
use App\Http\Resources\ListOptionFormCollection;
use App\Http\Resources\ListOptionFormDetailResource;
use App\Http\Controllers\Traits\ReturnErrorMessage;
use App\Http\Resources\FormHistoryCollection;



class FormController extends BaseApiController
{
    use AppData, ReturnErrorMessage;

    public function listForm(Request $request)
    {
        $user = $this->user;
        $data = $this->data;
        
        try {
            $form_group = FormGroup::whereHas('projects', function ($query) use ($request) {
                $query->where('id', $request->input('data.project_id'));
            })->whereHas('versions', function ($query) {
                $query->where('status', FormVersion::STATUS_ACTIVE);
            })
                ->with([
                'latestVersion.forms.formAttributes.locations',
                'latestVersion.forms.formAttributes.attribute',
                'latestVersion.forms.formAttributes.roles'
            ])
                ->get();
            
            if ($form_group->isEmpty()) {
                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data);
                
                return new BaseResource($emptyData);
            }
            
            array_push($data, $form_group);
            
            $form_group->appData = $this->prepareAppData($request, $data);
            
            return (new FormCollection($form_group))->additional([
                'AppData' => $this->appData
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function submit(Request $request)
    {
        $user = $this->user;
        $data = $this->data;
        
        $rules = [
            'data.form_group_id' => 'required',
            'data.location_id' => 'required|exists:locations,id',
            'data.input' => 'required|array'
        ];
        
        $request_holder = $request->all();
        foreach ($request->input('data')['input'] as $key => $val) {
            $holder = \DB::table('form_attribute_locations')->where('form_attribute_locations.id', $val['attribute_location_id'])
                ->join('form_attributes', 'form_attributes.id', '=', 'form_attribute_locations.form_attribute_id')
                ->join('attributes', 'attributes.id', '=', 'form_attributes.attribute_id')
                ->select([
                'attributes.*',
                'form_attribute_locations.value as dropdown_values'
            ])
                ->first();
            
            $preset_rule = $holder->preset_value;
            if ($holder->id == 9) {
                $preset_rule .= '|in:' . implode(',', explode('|', $holder->dropdown_values));
            }
            
            $rules['data.input.' . $key . '.attribute_location_id'] = 'required|exists:form_attribute_locations,id';
            $rules['data.input.' . $key . '.value'] = 'required|' . $preset_rule;
            
            $request_holder['data']['input'][$key]['attribute_id'] = $holder->id;
        }
        
        $request->replace($request_holder);
        
        $this->validate($request, $rules);
        
        $data = $request->all()['data'];
        
        $location_id = $data['location_id'];
        $last = Submission::where('location_id', $location_id)->count() + 1;
        $date = Carbon::now()->format('dmy');
        $form_group_id = $data['form_group_id'];
        $reference_no = "$date-F$form_group_id-L$location_id-R$last";
        
        try {
            \DB::beginTransaction();
            
            $submission = Submission::create([
                'reference_no' => $reference_no,
                'location_id' => $location_id,
                'user_id' => auth()->user()->id,
                'status_id' => GeneralStatus::where([
                    'name' => 'pending',
                    'type' => 'submission'
                ])->first()->id
            ]);
            
            $value = $data['input'];
            foreach ($value as $input) {
                
                switch ($input['attribute_id']) {
                    case 1: // long text
                    case 2: // short text
                    case 9: // dropdown box
                        SubmissionFormGroup::create([
                            'submission_id' => $submission->id,
                            'form_group_id' => $form_group_id,
                            'form_attribute_location_id' => $input['attribute_location_id'],
                            'value' => $input['value']
                        ]);
                        break;
                    case 3: // signature
                        $image_file = $input['value'];
                        
                        $name_unique = 'signature_' . time() . rand(10, 99) . '.png';
                        $store_path = Submission::FILE_PATH . '/' . $submission->id;
                        $path = public_path($store_path);
                        
                        if (! File::isDirectory($path)) {
                            File::makeDirectory($path, 0775, true);
                        }
                        
                        $image = Image::make($image_file->getRealPath());
                        
                        $size['width'] = $image->width();
                        $size['height'] = $image->height();
                        
                        $image->save($path . DIRECTORY_SEPARATOR . '' . $name_unique);
                        
                        SubmissionFormGroup::create([
                            'submission_id' => $submission->id,
                            'form_group_id' => $form_group_id,
                            'form_attribute_location_id' => $input['attribute_location_id'],
                            'value' => asset($store_path) . '/' . $name_unique
                        ]);
                        break;
                    case 5: // date
                        
                        $date_input = new Carbon($input['value']);
                        
                        SubmissionFormGroup::create([
                            'submission_id' => $submission->id,
                            'form_group_id' => $form_group_id,
                            'form_attribute_location_id' => $input['attribute_location_id'],
                            'value' => $date_input->format('d-m-Y')
                        ]);
                        break;
                    
                    case 6: // checkbox
                    case 7: // choice
                        $checkbox_input = $input['value'] == true || $input['value'] == 1 ? 1 : 0;
                        
                        SubmissionFormGroup::create([
                            'submission_id' => $submission->id,
                            'form_group_id' => $form_group_id,
                            'form_attribute_location_id' => $input['attribute_location_id'],
                            'value' => $checkbox_input
                        ]);
                        break;
                }
            }
            
            \DB::commit();
            
            $submission->inputs = collect($this->getListing([
                'submission_id' => $submission->id
            ]))->groupBy('form_id');
            $submission->appData = $this->prepareAppData($request, $data);
            
            return (new SubmissionResource($submission));
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    function getSubmissionListing(Request $request)
    {
        $user = $this->user;
        $data = $this->data;
        
        $submissions = Submission::where('location_id', $request->input('data.location_id'))->get();
        
        foreach ($submissions as $sKey => $sVal) {
            $submissions[$sKey]['inputs'] = collect($this->getListing([
                'submission_id' => $sVal->id
            ]))->groupBy('form_id');
        }
        
        $submissions->appData = $this->prepareAppData($request, $data);
        
        return (new SubmissionCollection($submissions))->additional([
            'AppData' => $this->appData
        ]);
    }

    private function getListing($param = [])
    {
        $select_columns = [
            'submission_form_group.value as input_value',
            'form_attribute_locations.position_x as input_position_x',
            'form_attribute_locations.position_y as input_position_y',
            'form_attribute_locations.height as input_height',
            'form_attribute_locations.width as input_width',
            'form_attributes.key as input_key',
            'form_attributes.attribute_id as input_id_type',
            'forms.id as form_id',
            'forms.height as form_height',
            'forms.width as form_width',
            'forms.file as form_file'
        ];
        
        return \DB::table('submission_form_group')->where($param)
            ->join('form_attribute_locations', 'submission_form_group.form_attribute_location_id', '=', 'form_attribute_locations.id')
            ->join('form_attributes', 'form_attribute_locations.form_attribute_id', '=', 'form_attributes.id')
            ->join('forms', 'form_attributes.form_id', '=', 'forms.id')
            ->select($select_columns)
            ->get();
    }

    /**
     * @SWG\Post(
     *     path="/form/list-option-form",
     *     summary="get location' forms listing",
     *     method="POST",
     *     tags={"Form"},
     *     description="This Api will retrieve a list of form that belongs to the location",
     *     operationId="listOptionForm",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="location_id",type="string",example="1"),
     *                      @SWG\Property(property="os",type="string",example="AND:0000"),
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
    public function listOptionForm(Request $request){

        $user = $this->user;
        $data = $this->data;

        if(!$location = LocationPoint::find($request->input('data.location_id'))){
            return $this->failData($request, $data, "Location not found.");
        }

        $normalFormArray = array();
        if($location->normal_form){
            $normalFormArray = explode(',', $location->normal_form);
        }

        $handOverFormArray = array();
        if($location->main_form){
            $handOverFormArray = explode(',', $location->main_form);
        }


        $formArray = array_merge($normalFormArray, $handOverFormArray);

        $form = FormGroup::whereIn('id', $formArray)
            ->whereHas('versions', function ($query) {
                $query->where('status', FormVersion::STATUS_ACTIVE);
            })  

            ->with([
                'latestVersion.forms.formAttributes.locations',
                'latestVersion.forms.formAttributes.attribute',
                'latestVersion.forms.formAttributes.roles'
            ])
            ->get();

        if ($form->isEmpty()) {
            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data);
            
            return new BaseResource($emptyData);
        }


        // $submission_arr = array();

        // if($submissionsLinkIssue = Submission::where('location_id', $location->id)->get()){

        //     foreach ($submissionsLinkIssue as $key => $value) {

        //         $temp_data = $value->linkIssue()->select('id')->get();
        //         foreach ($temp_data as $key => $value) {
        //             $submission_arr[] = $value->id;
        //         }
                
        //     }
        // }

        // $submission_arr = array_unique($submission_arr);
        // $link_issue = Issue::whereIn('id', $submission_arr)->get();


        // foreach ($form as $key => $value) {

        //     $form[$key]["location_id"] = $location->id;
        //     $form[$key]["link_issue"] = $link_issue;
        // }



        $project_id = $location->drawingPlan->drawingSet->project->id;
        
        $updated_at = '';
        if($form_update_status = ProjectDataStatus::where('project_id', $project_id)->where('data_name', 'form_status')->first()){
            $updated_at = $form_update_status->updated_at;
        }

        $detail =  (object) ['updated_at'       => $updated_at,
                            'form' => new ListOptionFormCollection($form)];

        array_push($data, $detail);

        $detail->appData = $this->prepareAppData($request, $data);

        return new ListOptionFormDetailResource($detail);

        // array_push($data, $form);
            
        // $form->appData = $this->prepareAppData($request, $data);



        // return (new FormCollection($form))->additional([
        //         'AppData' => $this->appData
        //     ]);
        
        // return (new ListOptionFormCollection($form))->additional(['AppData' => $this->appData]);

    }

   
    public function formSubmit(Request $request){

        $user = $this->user;
        $data = $this->data;


        if(!$form = FormGroup::with('formStatusOpen')->find($request->input('data.form_id'))){

            $status = $this->failedAppData('Form not found.');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if(!$location = LocationPoint::find($request->input('data.location_id'))){

            $status = $this->failedAppData('Location not found.');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }
        

        $last = Submission::where('location_id', $location->id)->count() + 1;
        $date = Carbon::now()->format('dmy');
        $form_group_id = $form->id;
        $reference_no = "$date-F$form_group_id-L$location->id-R$last";
    
        try {
            \DB::beginTransaction();
            
            $submission = Submission::create([
                'reference_no'  => $reference_no,
                'location_id'   => $location->id,
                'user_id'       => $user->id,
                'status_id'     => $request->input('data.status_id'),
                'form_group_id' => $form_group_id,
                'remarks'       => $request->input('data.remarks'),
            ]);

            if($request->input('data.issue_id')){
                
                $submission->linkIssue()->detach();

                $submission->linkIssue()->attach($request->input('data.issue_id'));
            }

            SubmissionHistory::create([
                'submission_id' => $submission->id,
                'remarks'       => 'Create Inspection Form.',
                'status_id'     => $form->formStatusOpen->id,
            ]);
            SubmissionHistory::create([
                'submission_id' => $submission->id,
                'remarks'       => $request->input('data.remarks'),
                'status_id'     => $request->input('data.status_id'),
            ]);


            $value = $request->all()['data']; //$request->input('data.input'); //TO GET ALL TYPE OF INPUT

            foreach ($value['input'] as $attribute_id => $form_attribute_location) {

                foreach ($form_attribute_location as $input_id => $input_value) {
                    
                    switch ($attribute_id) {
                        case 1: // long text
                            SubmissionFormGroup::create([
                                'submission_id' => $submission->id,
                                'form_attribute_location_id' => $input_id,
                                'value' => $input_value,
                            ]);
                            break;
                        case 2: // short text
                            SubmissionFormGroup::create([
                                'submission_id' => $submission->id,
                                'form_attribute_location_id' => $input_id,
                                'value' => $input_value,
                            ]);
                            break;
                        case 9: // dropdown box
                            SubmissionFormGroup::create([
                                'submission_id' => $submission->id,
                                'form_attribute_location_id' => $input_id,
                                'value' => $input_value,
                            ]);
                            break;
                        case 3: // signature
                            $image_file = $input_value;
                            
                            $name_unique = 'signature_' . time() . rand(10, 99) . '.png';
                            $store_path = Submission::FILE_PATH . '/' . $submission->id;
                            $path = public_path($store_path);
                            
                            if (! File::isDirectory($path)) {
                                File::makeDirectory($path, 0775, true);
                            }
                            
                            $image = Image::make($image_file->getRealPath());
                            
                            $size['width'] = $image->width();
                            $size['height'] = $image->height();
                            
                            $image->save($path . DIRECTORY_SEPARATOR . '' . $name_unique);
                            
                            SubmissionFormGroup::create([
                                'submission_id' => $submission->id,
                                'form_attribute_location_id' => $input_id,
                                'value' => asset($store_path) . '/' . $name_unique,
                            ]);
                            break;
                        case 5: // date
                            
                            $date_input = new Carbon($input_value);
                            
                            SubmissionFormGroup::create([
                                'submission_id' => $submission->id,
                                // 'form_group_id' => $form_group_id,
                                'form_attribute_location_id' => $input_id,
                                'value' => $date_input->format('d-m-Y')
                            ]);
                            break;
                        
                        case 6: // checkbox
                            SubmissionFormGroup::create([
                                'submission_id' => $submission->id,
                                'form_attribute_location_id' => $input_id,
                                'value' => $input_value == 1 ? 1 : 0,
                            ]);
                            break;
                        case 7: // choice
                            // $checkbox_input = $input_value == true || $input['value'] == 1 ? 1 : 0;
                            
                            SubmissionFormGroup::create([
                                'submission_id' => $submission->id,
                                'form_attribute_location_id' => $input_id,
                                'value' => $input_value == 1 ? 1 : 0,
                            ]);
                            break;
                    }

                }
            }
            
            \DB::commit();
            
            $submission->inputs = collect($this->getListing([
                'submission_id' => $submission->id
            ]))->groupBy('form_id');
            $submission->appData = $this->prepareAppData($request, $data);
            
            return (new SubmissionResource($submission));
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }

    }


    /**
     * @SWG\Post(
     *     path="/form/form-submission-list",
     *     summary="get list of submission form",
     *     method="POST",
     *     tags={"Form"},
     *     description="This Api will retrieve a list of form submission",
     *     operationId="formSubmissionList",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="location_id",type="string",example="1"),
     *                      @SWG\Property(property="os",type="string",example="AND:0000"),
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
    public function formSubmissionList(Request $request){

        $user = $this->user;
        $data = $this->data;

        $rules = [
            'location_id'         => 'required',
        ];

        $message = [
            'data.location_id.required'         => trans('api.required', ['field' => trans('api.location')]),
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);

        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }


        if(!$location = LocationPoint::find($request->input('data.location_id'))){

            $status = $this->failedAppData('Location not found');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }


        $submission = Submission::where('location_id', $request->input('data.location_id'))->get();

        if ($submission->isEmpty()) {
            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data);
            
            return new BaseResource($emptyData);
        }


        $form_id = Submission::where('location_id', $location->id)->groupBy('form_group_id')->select('form_group_id')->get();

        $formGroupCloseStatus = FormGroupStatus::whereIn('form_group_id', $form_id)->where('fix_label', 'closed')->select('id')->get();


        $formClose = Submission::where('location_id', $location->id)->whereIn('status_id', $formGroupCloseStatus)->get();
        $formOpen = Submission::where('location_id', $location->id)->whereNotIn('status_id', $formGroupCloseStatus)->get();


        $formGroupStatus =  (object) ['total_close' => count($formClose),
                                      'total_open'  => count($formOpen),
                                      'close'       => (new FormSubmissionColllection($formClose)),
                                      'open'        => (new FormSubmissionColllection($formOpen)),
                                     ];


        array_push($data, $formGroupStatus);

        $formGroupStatus->appData = $this->prepareAppData($request, $data);

        return new FormSubmissionListResource($formGroupStatus);
    }

    /**
     * @SWG\Post(
     *     path="/form/form-history",
     *     summary="get form history",
     *     method="POST",
     *     tags={"Form"},
     *     description="Use this Api to get history",
     *     operationId="formSubmitLinkIssue",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="submission_id",type="string",example="1"),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     * @param $string
     */
    public function formHistory(Request $request){

        $user = $this->user;
        $data = $this->data;

        $rules = [
            'submission_id'       => 'required',
        ];

        $message = [
            'data.submission_id.required'         => 'submission ID is required',
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);

        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        $history = SubmissionHistory::where('submission_id', $request->input('data.submission_id'))->latest()->get();

        if($history->isEmpty()){
            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data);
            
            return new BaseResource($emptyData);
        }

        array_push($data, $history);
            
        $history->appData = $this->prepareAppData($request, $data);

        return (new FormHistoryCollection($history))->additional([
                'AppData' => $this->appData
            ]);
    }

    /**
     * @SWG\Post(
     *     path="/form/all",
     *     summary="get all form to store in phone storage",
     *     method="POST",
     *     tags={"Form"},
     *     description="This Api will retrieve a list of form",
     *     operationId="allForm",
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
     *                      @SWG\Property(property="os",type="string",example="AND:0000"),
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
    public function allForm(Request $request){

        $user = $this->user;
        $data = $this->data;

        $rules = [
            'project_id'         => 'required',
        ];

        $message = [
            'data.project_id.required'         => trans('api.required', ['field' => trans('api.project')]),
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);

        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }


        if(!$project = project::find($request->input('data.project_id'))){

            $status = $this->failedAppData('Project not found');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }


        $arrayForm = [];
        $arrayGroupForm = [];
        foreach($project->digitalform as $form){

            array_push($arrayForm, $form->id);
        }


        foreach($project->groupDigitalform as $groupForm){

            foreach ($groupForm->form as $key => $value) {
                array_push($arrayGroupForm, $value->id);
            }
        }

        $form_id = array_merge($arrayForm, $arrayGroupForm);
        $form_id = array_unique($form_id);

        $form = FormGroup::whereIn('id', $form_id)
            ->whereHas('versions', function ($query) {
                $query->where('status', FormVersion::STATUS_ACTIVE);
            })  

            ->with([
                'latestVersion.forms.formAttributes.locations',
                'latestVersion.forms.formAttributes.attribute',
                'latestVersion.forms.formAttributes.roles'
            ])
            ->get();

        if ($form->isEmpty()) {
            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data);
            
            return new BaseResource($emptyData);
        }

        array_push($data, $form);
            
        $form->appData = $this->prepareAppData($request, $data);

        return (new AllFormCollection($form))->additional([
                'AppData' => $this->appData
            ]);



    }


    public function formUpdate(Request $request){

        $user = $this->user;
        $data = $this->data;
        

        $date = Carbon::now()->format('dmy');
        try {
            \DB::beginTransaction();
            
            if (!$submission = Submission::find($request->input('data.submission_id'))) {

                $status = $this->failedAppData('submision not found');

                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }

            $submission->update([
                'status_id'     => $request->input('data.status_id'),
            ]);

            if($request->input('data.issue_id')){
                $submission->linkIssue()->detach();
                $submission->linkIssue()->attach($request->input('data.issue_id'));
            }

            SubmissionHistory::create([
                'submission_id' => $submission->id,
                'remarks'       => $request->input('data.remarks'),
                'status_id'     => $request->input('data.status_id'),
            ]);

            SubmissionFormGroup::where('submission_id', $submission->id)->delete();

            $value = $request->all()['data']; //$request->input('data.input'); //TO GET ALL TYPE OF INPUT

            foreach ($value['input'] as $attribute_id => $form_attribute_location) {

                foreach ($form_attribute_location as $input_id => $input_value) {
                    
                    switch ($attribute_id) {
                        case 1: // long text
                            SubmissionFormGroup::create([
                                'submission_id' => $submission->id,
                                'form_attribute_location_id' => $input_id,
                                'value' => $input_value,
                            ]);
                            break;
                        case 2: // short text
                            SubmissionFormGroup::create([
                                'submission_id' => $submission->id,
                                'form_attribute_location_id' => $input_id,
                                'value' => $input_value,
                            ]);
                            break;
                        case 9: // dropdown box
                            SubmissionFormGroup::create([
                                'submission_id' => $submission->id,
                                'form_attribute_location_id' => $input_id,
                                'value' => $input_value,
                            ]);
                            break;
                        case 3: // signature
                            $image_file = $input_value;
                            
                            $name_unique = 'signature_' . time() . rand(10, 99) . '.png';
                            $store_path = Submission::FILE_PATH . '/' . $submission->id;
                            $path = public_path($store_path);
                            
                            if (! File::isDirectory($path)) {
                                File::makeDirectory($path, 0775, true);
                            }
                            
                            $image = Image::make($image_file->getRealPath());
                            
                            $size['width'] = $image->width();
                            $size['height'] = $image->height();
                            
                            $image->save($path . DIRECTORY_SEPARATOR . '' . $name_unique);
                            
                            SubmissionFormGroup::create([
                                'submission_id' => $submission->id,
                                'form_attribute_location_id' => $input_id,
                                'value' => asset($store_path) . '/' . $name_unique,
                            ]);
                            break;
                        case 5: // date
                            
                            $date_input = new Carbon($input_value);
                            
                            SubmissionFormGroup::create([
                                'submission_id' => $submission->id,
                                // 'form_group_id' => $form_group_id,
                                'form_attribute_location_id' => $input_id,
                                'value' => $date_input->format('d-m-Y')
                            ]);
                            break;
                        
                        case 6: // checkbox
                            SubmissionFormGroup::create([
                                'submission_id' => $submission->id,
                                'form_attribute_location_id' => $input_id,
                                'value' => $input_value == 1 ? 1 : 0,
                            ]);
                            break;
                        case 7: // choice
                            // $checkbox_input = $input_value == true || $input['value'] == 1 ? 1 : 0;
                            
                            SubmissionFormGroup::create([
                                'submission_id' => $submission->id,
                                'form_attribute_location_id' => $input_id,
                                'value' => $input_value == 1 ? 1 : 0,
                            ]);
                            break;
                    }

                }
            }
            
            \DB::commit();
            
            $submission->inputs = collect($this->getListing([
                'submission_id' => $submission->id
            ]))->groupBy('form_id');
            $submission->appData = $this->prepareAppData($request, $data);
            
            return (new SubmissionResource($submission));
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }

    }
}

