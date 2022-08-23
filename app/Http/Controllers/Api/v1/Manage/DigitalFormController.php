<?php

namespace App\Http\Controllers\Api\v1\Manage;

use File;
use Carbon\Carbon;
use App\Supports\AppData;
use App\Entity\User;
use App\Entity\RoleUser;
use App\Entity\FormGroup;
use App\Entity\LocationPoint;
use App\Entity\Issue;
use App\Entity\History;
use App\Entity\Submission;
use App\Entity\SubmissionHistory;
use App\Entity\SubmissionFormGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\Api\v1\BaseApiController;
use App\Http\Resources\SubmissionResource;
use App\Http\Resources\IssueCollection;
use App\Http\Controllers\Traits\ReturnErrorMessage;
use App\Http\Controllers\Traits\PushNotification;


class DigitalFormController extends BaseApiController
{
    use AppData, ReturnErrorMessage;

    /**
     * @SWG\Post(
     *     path="/form/form-create",
     *     summary="Create Digital Form",
     *     method="POST",
     *     tags={"Form"},
     *     description="This Api will create digital form",
     *     operationId="create",
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
     *                      @SWG\Property(property="form_id",type="string",example="1"),
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
    public function create(Request $request){

        $user = $this->user;
        $data = $this->data;

        try {
            \DB::beginTransaction();

	        if(!$form = FormGroup::find($request->input('data.form_id'))){
	            return $this->failData($request, $data, "Location not found.");
	        }

	        if(!$location = LocationPoint::find($request->input('data.location_id'))){
	            return $this->failData($request, $data, "Location not found.");
	        }

            return $form;

	        $last = Submission::where('location_id', $location->id)->count() + 1;
	        $date = Carbon::now()->format('dmy');
	        $form_group_id = $form->id;
	        $reference_no = "$date-F$form_group_id-L$location->id-R$last";

	        $submission = Submission::create([
            	'reference_no'     => $reference_no,
                'location_id'       => $location->id,
                'user_id'           => $user->id,
                'status_id'         => $form->formStatusOpen->id,
                'form_version_id'   => $form_group_id,
            ]);

            SubmissionHistory::create([
                'submission_id' => $submission->id,
                'remarks'       => 'Create Inspection Form.',
                'status_id'     => $form->formStatusOpen->id,
            ]);
            
            \DB::commit();

            $submission = collect(['submission_id' => $submission->id]);
	        $submission->appData = $this->prepareAppData($request, $data);

	        return new BaseResource($submission);

            
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }

    }

    /**
     * @SWG\Post(
     *     path="/form/form-submit",
     *     summary="submit digital form",
     *     method="POST",
     *     tags={"Form"},
     *     description="This Api will submit details form insert by user",
     *     operationId="submit",
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
     *                      @SWG\Property(property="input",type="string",example=""),
     *                      @SWG\Property(property="status_id",type="string",example="1"),
     *                      @SWG\Property(property="remarks",type="string",example="this location is ready."),
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
    public function submit(Request $request){

    	$user = $this->user;
        $data = $this->data;

            
        if(!$submission = Submission::find($request->input('data.submission_id'))){
            return $this->failData($request, $data, "Submission not found.");
        }

        try {
            \DB::beginTransaction();

            SubmissionHistory::create([
                'submission_id' => $submission->id,
                'remarks'       => $request->input('data.remarks'),
                'status_id'     => $request->input('data.status_id'),
            ]);

            $submission->update([
                'status_id'     => $request->input('data.status_id'),
            ]);


            $value = $request->all()['data'];

            SubmissionFormGroup::where('submission_id', $submission->id)->delete();
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
                            
                            $image = Image::make($image_file);
                            
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
     *     path="/form/link-issue",
     *     summary="link issue with form",
     *     method="POST",
     *     tags={"Form"},
     *     description="This Api will submit the link issue",
     *     operationId="linkIssue",
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
     *                      @SWG\Property(property="issue_id",type="string",example=""),
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
    public function linkIssue(Request $request){

    	$user = $this->user;
        $data = $this->data;

            
        if(!$submission = Submission::find($request->input('data.submission_id'))){
            return $this->failData($request, $data, "Location not found.");
        }

        try {
            \DB::beginTransaction();

            $submission->linkIssue()->detach();

            $submission->linkIssue()->attach($request->input('data.issue_id'));
            
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
     *     path="/form/list-link-issue",
     *     summary="link issue with form",
     *     method="POST",
     *     tags={"Form"},
     *     description="This Api will submit the link issue",
     *     operationId="listLinkIssue",
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
    public function listLinkIssue(Request $request){

    	$user = $this->user;
        $data = $this->data;

            
        if(!$submission = Submission::find($request->input('data.submission_id'))){
            return $this->failData($request, $data, "Location not found.");
        }

        try {
            \DB::beginTransaction();

            $issue_id_arr = [];
            foreach ($submission->linkIssue as $key => $value) {

            	$issue_id_arr[] = $value["id"];
            };



            $issue = Issue::whereIn('id', $issue_id_arr)->get();

            if ($issue->isEmpty()) {
	            $emptyData = collect();
	            $emptyData->appData = $this->prepareAppData($request, $data);
	            
	            return new BaseResource($emptyData);
	        }

            $issue->appData = $this->prepareAppData($request, $data);
        
        	return (new IssueCollection($issue))->additional([
            	'AppData' => $this->appData
       		]);

            \DB::commit();

        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
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
     *     path="/form/owner-form-submit",
     *     summary="Owner Create Digital Form",
     *     method="POST",
     *     tags={"Form"},
     *     description="This Api will create digital form, link issues and submit the form",
     *     operationId="ownersubmit",
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
     *                      @SWG\Property(property="form_id",type="string",example="1"),
     *                      @SWG\Property(property="issue_id",type="string",example=""),
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
    public function ownerFormSubmit(Request $request){
        $user = $this->user;
        $data = $this->data;

        //First step: create form
        try {
            \DB::beginTransaction();

            if(!$form = FormGroup::find($request->input('data.form_id'))){
                return $this->failData($request, $data, "Location not found.");
            }

            if(!$location = LocationPoint::find($request->input('data.location_id'))){
                return $this->failData($request, $data, "Location not found.");
            }

            $last = Submission::where('location_id', $location->id)->count() + 1;
            $date = Carbon::now()->format('dmy');
            $form_group_id = $form->id;
            $reference_no = "$date-F$form_group_id-L$location->id-R$last";

            $submission = Submission::create([
                'reference_no'      => $reference_no,
                'location_id'       => $location->id,
                'user_id'           => $user->id,
                'status_id'         => $form->formStatusOpen->id,
                'form_version_id'   => $form_group_id,
            ]);

            //Second step: link issue to the form
            $submission->linkIssue()->attach($request->input('data.issue_id'));

            //Third step: submit form
            $value = $request->all()['data'];

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
                            
                            $image = Image::make($image_file);
                            
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
                            
                            SubmissionFormGroup::create([
                                'submission_id' => $submission->id,
                                'form_attribute_location_id' => $input_id,
                                'value' => $input_value == 1 ? 1 : 0,
                            ]);
                            break;
                    }

                }
            }

            $submission->inputs = collect($this->getListing([
                'submission_id' => $submission->id
            ]))->groupBy('form_id');
            $submission->appData = $this->prepareAppData($request, $data);

            //forth step: store issue as complete
            foreach ($request->input('data.issue_id') as $key => $value) {
                $issue = Issue::find($value);

                //close issue
                $issue->update(['status_id' => 10]);

                $history = History::create([
                    'user_id'               => $user->id,
                    'issue_id'              => $value,
                    'status_id'             => $issue->status_id,
                    'customer_view'         => 0,
                ]);

                $message =  'Issue('. $issue->reference .') is changed to "Closed" status.';

                $appData = array('type' => 'close_issue','project_id' => $issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $issue->location->drawingPlan->id ,'location_id' => $issue->location_id,'issue_id' => $issue->id, 'show_in_foreground' => true);

                $contractor = RoleUser::where('role_id', 5)->where('group_id', $issue->group_id)->select('user_id')->get();
                $this->FCMnotification($title, $message, $appData, $contractor, $issue->id, $issue->created_by);
                $inspector = User::where('id', $issue->inspector_id)->select('id as user_id')->get();
                $this->FCMnotification($title, $message, $appData, $inspector, $issue->id, $issue->created_by);
                
                if (!is_null($request->file('data.image')) && count($request->file('data.image')) > 0) {
                    foreach ($request->file('data.image') as $key => $value) {

                        $image = \App\Processors\SaveIssueProcessor::make($value)->execute();

                        $history->images()->create([
                            'image' => $image,
                        ]);
                    }
                }
            }

            return (new SubmissionResource($submission));

            \DB::commit();
            
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }
}
