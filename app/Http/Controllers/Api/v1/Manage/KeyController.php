<?php

namespace App\Http\Controllers\Api\v1\Manage;

use Validator;
use Carbon\Carbon;
use App\Supports\AppData;
use App\Entity\Issue;
use App\Entity\Project;
use App\Entity\RoleUser;
use App\Entity\History;
use App\Entity\DrawingPlan;
use App\Entity\DrawingSet;
use App\Entity\ItemSubmitted;
use App\Entity\LocationPoint;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Entity\ItemSubmittedTransaction;
use App\Http\Controllers\Api\v1\BaseApiController;
use App\Http\Controllers\Traits\AccessItems;
use App\Http\Controllers\Traits\ReturnErrorMessage;
use App\Http\Controllers\Traits\PushNotification;



class KeyController extends BaseApiController
{
    use AppData, ReturnErrorMessage, AccessItems, PushNotification;
    
    /**
     * @SWG\Post(
     *     path="/key",
     *     summary="To manage key",
     *     method="post",
     *     tags={"Key"},
     *     description="This Api will manage key.",
     *     operationId="manageKey",
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
     *                      @SWG\Property(property="status",type="string",example="submit"),
     *                      @SWG\Property(property="type",type="string",example="individual"),
     *                      @SWG\Property(property="items",type="string",example=""),
     *                      @SWG\Property(property="possessor",type="string",example="management"),
     *                      @SWG\Property(property="name_submit",type="string",example="Desmond"),
     *                      @SWG\Property(property="name_receive",type="string",example="Ryan"),
     *                      @SWG\Property(property="signature_submit",type="string",example=""),
     *                      @SWG\Property(property="signature_receive",type="string",example=""),
     *                      @SWG\Property(property="remarks",type="string",example="This is remarks"),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function keyManagement(Request $request)
    {
        $newDate = date("Ymd", strtotime(Carbon::now()));
        // dd($newDate.'ddd');
        // $generated_code = "AI".date("Ymd", strtotime(Carbon::now())).'-'.rand(10000000, 99999999);
        
        $data = $this->data;
        $user = $this->user;

        $rules = [
            // 'drawing_plan_id'       => 'required',
            'status'                => 'required', // example: submit or return
            'type'                  => 'required', // example: individual or batch 
            'items'                 => 'required', // example
            'possessor'             => 'required',
            'name_submit'           => 'required', // example: owner1
            'name_receive'          => 'required', // example: management1
            // 'signature_submit'      => 'required', //image
            // 'signature_receive'     => 'required', //image
        ];

        $message = [
            'drawing_plan_id.required'         => "Drawing Plan ID is required.",
            'status.required'                  => "Status is required.",
            'type.required'                    => "Type is required.",
            'items.required'                   => "Itemsss is required.",
            'possessor.required'               => "Possessor is required.",
            'name_submit.required'             => "Name submit is required.",
            'name_receive.required'            => "Name receive is required.",
            'signature_submit.required'        => "Signature submit is required.",
            'signature_receive.required'       => "Signature receive is required.",
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);


        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        ##CHECK IF USER ROLE IS PROJECT TEAM
        if($user->current_role != 8)
        {
            $status = $this->failedAppData("Unauthorized access.");

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        if($request->input('data')['status'] == "submit")
        {
            if($request->input('data')['type'] == "individual")
            {
                if($request->input('data')['possessor'] == "handler")
                {
                    return $this->submitHandler($request, $user);
                }
                else if($request->input('data')['possessor'] == "management")
                {
                    return $this->submitManagement($request, $user);
                }
            }
            else if($request->input('data')['type'] == "batch")
            {
                return $this->batchSubmit($request, $user);
            }
        }
        else if($request->input('data')['status'] == "return")
        {
            if($request->input('data')['type'] == "individual")
            {
                if($request->input('data')['possessor'] == "management")
                {
                    return $this->returnManagement($request, $user);
                }
                else if($request->input('data')['possessor'] == "unit owner")
                {
                    return $this->returnUnitOwner($request, $user);
                }
            }
            else if($request->input('data')['type'] == "batch")
            {
                return $this->batchReturn($request, $user);
            }
        }
    }

    /**
     * @SWG\Post(
     *     path="/key/getAllKey",
     *     summary="To get all item key",
     *     method="post",
     *     tags={"Key"},
     *     description="This Api will all item key.",
     *     operationId="getAllKey",
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
    public function getAllKey(Request $request)
    {
        $data = $this->data;
        $user = $this->user;

        $rules = [
            'project_id'                => 'required', 
        ];

        $message = [
            'project_id.required'         => "Project ID is required.",
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);

        ##CHECK IF PROJECT IS EXIST
        if(!$project = Project::find($request->input('data.project_id'))){
            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, ["message"=>"Project are not exist."]);

            return new BaseResource($emptyData);
        }

        ##CHECK IF USER IS INVOLVE in thiS PROJECT // inspector
        if($user->current_role == 4 || $user->current_role == 8 || $user->current_role == 7){

            if(!$role_user = RoleUser::where('user_id', $user->id)->where('project_id', $project->id)->where('role_id', $user->current_role)->where('client_id', $project->client_id)->first())
            {

                $status = $this->failedAppData('Cannot access to this project.');
                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);
                return new BaseResource($emptyData);
            }  
        }

        ##CHECK IF USER IS INVOLVE in thiS PROJECT // Contractor
        if($user->current_role == 5){
            $group_id = RoleUser::where("user_id", $user->id)
                        ->groupBy('group_id')
                        ->select('group_id')
                        ->get();

            if(!$project_user  = GroupProject::where('project_id', $project->id)->whereIn("group_id", $group_id)
                        ->get())
            {
                $status = $this->failedAppData('Cannot access to this project.');
                $emptyData = collect();
                $emptyData->appData = $this->prepareAppData($request, $data, $status);
                return new BaseResource($emptyData);
            }

        }

        ##GET DRAWING SET_ID BELONG To THE PROJECT
        if(!$drawingSet = DrawingSet::where('project_id', $project->id)->select('id')->get()){
            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, ["message"=>"There are no drawing set or drawing plan in this project."]);

            return new BaseResource($emptyData);
        }

        return $this->AccessItems($user, $data, $request, $project->id);

    }

    public function submitManagement($request, $user)
    {
        $data = $request->input('data');
        $signature_submit = $request->file('data')['signature_submit'];
        $signature_receive = $request->file('data')['signature_receive'];
        $transaction_items = [];

        for($i=0; $i<count($data['items']); $i++)
        {
            for($j=0;$j<$data['items'][$i]['quantity'];$j++)
            {
                $item_submitted = ItemSubmitted::create([
                    'drawing_plan_id'   => $data['drawing_plan_id'],
                    'code'              => $data['items'][$i]['code'] ? $data['items'][$i]['code'] : "",
                    'name'              => $data['items'][$i]['name'],
                    'possessor'         => $data['possessor'],
                    'created_by'        => $user->id,
                    'updated_by'        => $user->id,
                ]);
            }

            $transaction_item = (object)[
                "code"      => $item_submitted->code,
                "name"      => $item_submitted->name,
                "quantity"  => $data['items'][$i]['quantity'],
            ];

            array_push($transaction_items, $transaction_item);
        }

        $item_submitted_transaction = ItemSubmittedTransaction::create([
            'items'                         => $transaction_items,
            'code'                          => "AI".date("Ymd", strtotime(Carbon::now())).'-'.rand(10000000, 99999999),
            'status'                        => "receive",
            'drawing_plan_id'               => $data['drawing_plan_id'],
            'signature_receive'             => \App\Processors\SaveSignatureProcessor::make($signature_receive)->execute(),
            'signature_submit'              => \App\Processors\SaveSignatureProcessor::make($signature_submit)->execute(),
            'signature_receive_datetime'    => $data['signature_receive_datetime'],
            'signature_submit_datetime'     => $data['signature_submit_datetime'],
            'name_receive'                  => $data['name_receive'],
            'name_submit'                   => $data['name_submit'],
            'internal_remarks'              => isset($data['remarks']) ? $data['remarks'] : "",
            'external_remarks'              => isset($data['external_remarks']) ? $data['external_remarks'] : "",
            'created_by'                    => $user->id,
            'updated_by'                    => $user->id,
        ]);

        $now = Carbon::now();
        $time_now = date("H:i:s", strtotime($now));
        $morning = '00:00:00';
        //change back to 13:00:00 after monday presentation
        $afternoon = '15:00:00';

        $drawing_plan = DrawingPlan::where('id', $data['drawing_plan_id'])->first();

        // if($afternoon>$time_now)
        // {
            $item_submitted_count = ItemSubmitted::where('drawing_plan_id', $drawing_plan->id)->count();

            if($item_submitted_count>0)
            {
                $locations = LocationPoint::where('drawing_plan_id', $drawing_plan->id)->get();

                foreach($locations as $location)
                {   
                    $pending_access_issues = Issue::where('location_id', $location->id)->where('status_id', 13)->get();

                    if(count($pending_access_issues)>0)
                    {
                        foreach($pending_access_issues as $pending_access_issue)
                        {
                            $pending_access_issue->update([
                                'status_id' => 5,
                            ]);

                            $history = History::create([
                                'user_id'               => $user->id,
                                'issue_id'              => $pending_access_issue->id,
                                'status_id'             => $pending_access_issue->status_id,
                                'remarks'               => "Issue status changed to W.I.P",
                                'customer_view'         => 1,
                            ]);

                            $title =  $pending_access_issue->location->drawingPlan->drawingSet->project->name;
                            $message =  'Issue('. $pending_access_issue->reference .') is changed to "W.I.P" status.';

                            $appData = array('type' => 'accept_issue','project_id' => $pending_access_issue->location->drawingPlan->drawingSet->project_id ,'plan_id' => $pending_access_issue->location->drawingPlan->id ,'location_id' => $pending_access_issue->location_id,'issue_id' => $pending_access_issue->id, 'show_in_foreground' => true);

                            $contractor = RoleUser::where('role_id', 5)->where('group_id', $pending_access_issue->group_id)->select('user_id')->get();
                            $this->FCMnotification($title, $message, $appData, $contractor, $pending_access_issue->id, $pending_access_issue->created_by);

                            if($pending_access_issue->inspector_id!=null)
                            {

                                $inspector = RoleUser::where('user_id', $pending_access_issue->inspector_id)->select('user_id')->get();
                                $this->FCMnotification($title, $message, $appData, $inspector, $pending_access_issue->id, $history->user_id);  
                            }
                        }
                    }
                }
            }
        // }

        return $this->emptyData($request, []);
    }

    public function submitHandler($request, $user)
    {
        $data=$request->input('data');
        $signature_submit = $request->file('data')['signature_submit'];
        $signature_receive = $request->file('data')['signature_receive'];
        $transaction_items = [];

        for ($i=0; $i<count($data['items']); $i++)
        {
            //check if the item is available
            if(ItemSubmitted::where('id', $data['items'][$i])->count()==0)
            {
                $status = $this->failedAppData("Item is not available.");

                $emptyData = collect();

                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }

            $temp_item = ItemSubmitted::where('id', $data['items'][$i])->first();
            //check if the item is in managemnt side
            if($temp_item->possessor != "management")
            {
                $status = $this->failedAppData("Item is already passed to handler.");

                $emptyData = collect();

                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }
        }

        $item_submitted=ItemSubmitted::whereIn('id', $data['items'])->groupBy('name')->groupBy('code')->get();
        ItemSubmitted::whereIn('id', $data['items'])->update(['possessor'=>$data['possessor']]);
        $transaction_items=[];

        for($i=0;$i<count($item_submitted);$i++)
        {
            $temp_count = ItemSubmitted::whereIn('id', $data['items'])->where('name', $item_submitted[$i]['name'])->count();

            $transaction_item = (object)[
                "code"      => $item_submitted[$i]['code'],
                "name"      => $item_submitted[$i]['name'],
                "quantity"  => $temp_count,
            ];

            array_push($transaction_items, $transaction_item);
        }

        $item_submitted_transaction = ItemSubmittedTransaction::create([
            'code'                          => "AI".date("Ymd", strtotime(Carbon::now())).'-'.rand(10000000, 99999999),
            'items'                         => $transaction_items,
            'status'                        => "handover_submit",
            'drawing_plan_id'               => $data['drawing_plan_id'],
            'signature_receive'             => \App\Processors\SaveSignatureProcessor::make($signature_receive)->execute(),
            'signature_submit'              => \App\Processors\SaveSignatureProcessor::make($signature_submit)->execute(),
            'signature_receive_datetime'    => $data['signature_receive_datetime'],
            'signature_submit_datetime'     => $data['signature_submit_datetime'],
            'name_receive'                  => $data['name_receive'],
            'name_submit'                   => $data['name_submit'],
            'internal_remarks'              => isset($data['remarks']) ? $data['remarks'] : "",
            'external_remarks'              => isset($data['external_remarks']) ? $data['external_remarks'] : "",
            'created_by'                    => $user->id,
            'updated_by'                    => $user->id,
        ]);

        $item_submitted_transaction->save();

        return $this->emptyData($request, []);
    }

    public function returnManagement($request, $user)
    {
        $data=$request->input('data');
        $signature_submit = $request->file('data')['signature_submit'];
        $signature_receive = $request->file('data')['signature_receive'];
        $transaction_items = [];

        for ($i=0; $i<count($data['items']); $i++)
        {
            //check if the item is available
            if(ItemSubmitted::where('id', $data['items'][$i])->count()==0)
            {
                $status = $this->failedAppData("Item is not available.");

                $emptyData = collect();

                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }

            $temp_item = ItemSubmitted::where('id', $data['items'][$i])->first();
            //check if the item is in managemnt side
            if($temp_item->possessor != "handler")
            {
                $status = $this->failedAppData("Item is already passed to management.");

                $emptyData = collect();

                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }
        }
        
        $item_submitted=ItemSubmitted::whereIn('id', $data['items'])->groupBy('name')->groupBy('code')->get();
        ItemSubmitted::whereIn('id', $data['items'])->update(['possessor'=>$data['possessor']]);
        $transaction_items=[];

        for($i=0;$i<count($item_submitted);$i++)
        {
            $temp_count = ItemSubmitted::whereIn('id', $data['items'])->where('name', $item_submitted[$i]['name'])->count();

            $transaction_item = (object)[
                "code"      => $item_submitted[$i]['code'],
                "name"      => $item_submitted[$i]['name'],
                "quantity"  => $temp_count,
            ];

            array_push($transaction_items, $transaction_item);
        }

        $item_submitted_transaction = ItemSubmittedTransaction::create([
            'code'                          => "AI".date("Ymd", strtotime(Carbon::now())).'-'.rand(10000000, 99999999),
            'items'                         => $transaction_items,
            'status'                        => "handover_return",
            'drawing_plan_id'               => $data['drawing_plan_id'],
            'signature_receive'             => \App\Processors\SaveSignatureProcessor::make($signature_receive)->execute(),
            'signature_submit'              => \App\Processors\SaveSignatureProcessor::make($signature_submit)->execute(),
            'signature_receive_datetime'    => $data['signature_receive_datetime'],
            'signature_submit_datetime'     => $data['signature_submit_datetime'],
            'name_receive'                  => $data['name_receive'],
            'name_submit'                   => $data['name_submit'],
            'internal_remarks'              => isset($data['remarks']) ? $data['remarks'] : "",
            'external_remarks'              => isset($data['external_remarks']) ? $data['external_remarks'] : "",
            'created_by'                    => $user->id,
            'updated_by'                    => $user->id,
        ]);

        return $this->emptyData($request, []);
    }

    public function returnUnitOwner($request, $user)
    {
        $data=$request->input('data');
        $signature_submit = $request->file('data')['signature_submit'];
        $signature_receive = $request->file('data')['signature_receive'];
        $transaction_items = [];

        for ($i=0; $i<count($data['items']); $i++)
        {
            //check if the item is available
            if(ItemSubmitted::where('id', $data['items'][$i])->count()==0)
            {
                $status = $this->failedAppData("Item is not available.");

                $emptyData = collect();

                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }

            $temp_item = ItemSubmitted::where('id', $data['items'][$i])->first();
            //check if the item is in managemnt side
            if($temp_item->possessor != "management")
            {
                $status = $this->failedAppData("Item is not returned to management.");

                $emptyData = collect();

                $emptyData->appData = $this->prepareAppData($request, $data, $status);

                return new BaseResource($emptyData);
            }
        }
        
        $item_submitted=ItemSubmitted::whereIn('id', $data['items'])->groupBy('name')->groupBy('code')->get();
        
        $transaction_items=[];

        for($i=0;$i<count($item_submitted);$i++)
        {
            $temp_count = ItemSubmitted::whereIn('id', $data['items'])->where('name', $item_submitted[$i]['name'])->count();

            $transaction_item = (object)[
                "code"      => $item_submitted[$i]['code'],
                "name"      => $item_submitted[$i]['name'],
                "quantity"  => $temp_count,
            ];

            array_push($transaction_items, $transaction_item);
        }

        ItemSubmitted::whereIn('id', $data['items'])->delete();


        // for ($i=0; $i<count($data['items']); $i++)
        // {
        //     //check if the item is available
        //     if(ItemSubmitted::where('id', $data['items'][$i]['id'])->count()==0)
        //     {
        //         $status = $this->failedAppData("Item is not available.");

        //         $emptyData = collect();

        //         $emptyData->appData = $this->prepareAppData($request, $data, $status);

        //         return new BaseResource($emptyData);
        //     }

        //     $temp_item = ItemSubmitted::where('id', $data['items'][$i]['id'])->first();
        //     //check if the item is in returned to management before retur nto unit owner
        //     if($temp_item->possessor != "handler")
        //     {
        //         $status = $this->failedAppData("Key is not returned to manangement.");

        //         $emptyData = collect();

        //         $emptyData->appData = $this->prepareAppData($request, $data, $status);

        //         return new BaseResource($emptyData);
        //     }
        // }

        // //return the key to unit owner //delete submitted items
        // for($i=0; $i<count($data['items']); $i++)
        // {
        //     $item_submitted = ItemSubmitted::where('id', $data['items'][$i]['id'])->first();

        //     $transaction_item = (object)[
        //         "id"    => $item_submitted->id,
        //         "code"  => $item_submitted->code,
        //         "name"  => $item_submitted->name,
        //     ];

        //     array_push($transaction_items, $transaction_item);

        //     $item_submitted->delete();  
        // }

        $item_submitted_transaction = ItemSubmittedTransaction::create([
            'code'                          => "AI".date("Ymd", strtotime(Carbon::now())).'-'.rand(10000000, 99999999),
            'items'                         => $transaction_items,
            'status'                        => "return",
            'drawing_plan_id'               => $data['drawing_plan_id'],
            'signature_receive'             => \App\Processors\SaveSignatureProcessor::make($signature_receive)->execute(),
            'signature_submit'              => \App\Processors\SaveSignatureProcessor::make($signature_submit)->execute(),
            'signature_receive_datetime'    => $data['signature_receive_datetime'],
            'signature_submit_datetime'     => $data['signature_submit_datetime'],
            'name_receive'                  => $data['name_receive'],
            'name_submit'                   => $data['name_submit'],
            'internal_remarks'              => isset($data['remarks']) ? $data['remarks'] : "",
            'external_remarks'              => isset($data['external_remarks']) ? $data['external_remarks'] : "",
            'created_by'                    => $user->id,
            'updated_by'                    => $user->id,
        ]);

        return $this->emptyData($request, []);
    }

    public function batchSubmit($request, $user)
    {
        $data=$request->input('data');
        $signature_submit = $request->file('data')['signature_submit'];
        $signature_receive = $request->file('data')['signature_receive'];

        for($i=0; $i<count($data['items']); $i++)
        {
            for($j=0; $j<count($data['items'][$i]['items']); $j++)
            {
                //check if the itme is available
                if(ItemSubmitted::where('id', $data['items'][$i]['items'][$j])->count()==0)
                {
                    $status = $this->failedAppData("Item is not available.");

                    $emptyData = collect();

                    $emptyData->appData = $this->prepareAppData($request, $data, $status);

                    return new BaseResource($emptyData);   
                }

                $temp_item = ItemSubmitted::where('id', $data['items'][$i]['items'][$j])->first();

                //check if the key is currently at management side 
                if($temp_item->possessor != "management")
                {
                    $status = $this->failedAppData("Item is already passed to handler.");

                    $emptyData = collect();

                    $emptyData->appData = $this->prepareAppData($request, $data, $status);

                    return new BaseResource($emptyData);
                }

            }
        }

        for($i=0; $i<count($data['items']); $i++)
        {
            $transaction_items = [];
            $item_submitted = ItemSubmitted::whereIn('id', $data['items'][$i]['items'])->groupBy('name')->groupBy('code')->get();
            ItemSubmitted::whereIn('id', $data['items'][$i]['items'])->update(['possessor'=>$data['possessor']]);

            for($j=0;$j<count($item_submitted);$j++)
            {
                $temp_count = ItemSubmitted::whereIn('id', $data['items'][$i]['items'])->where('name', $item_submitted[$j]['name'])->count();

                $transaction_item = (object)[
                    "code"      => $item_submitted[$j]['code'],
                    "name"      => $item_submitted[$j]['name'],
                    "quantity"  => $temp_count,
                ];

                array_push($transaction_items, $transaction_item);
            }

            //store transaction
            $item_submitted_transaction = ItemSubmittedTransaction::create([
                'code'                          => "AI".date("Ymd", strtotime(Carbon::now())).'-'.rand(10000000, 99999999),
                'items'                         => $transaction_items,
                'status'                        => "handover_submit",
                'drawing_plan_id'               => $data['items'][$i]['drawing_plan_id'],
                'signature_receive'             => \App\Processors\SaveSignatureProcessor::make($signature_receive)->execute(),
                'signature_submit'              => \App\Processors\SaveSignatureProcessor::make($signature_submit)->execute(),
                'signature_receive_datetime'    => $data['signature_receive_datetime'],
                'signature_submit_datetime'     => $data['signature_submit_datetime'],
                'name_receive'                  => $data['name_receive'],
                'name_submit'                   => $data['name_submit'],
                'internal_remarks'              => isset($data['remarks']) ? $data['remarks'] : "",
                'external_remarks'              => isset($data['external_remarks']) ? $data['external_remarks'] : "",
                'created_by'                    => $user->id,
                'updated_by'                    => $user->id,
            ]); 

        }

        return $this->emptyData($request, []);

    }

    public function batchReturn($request, $user)
    {
        $data=$request->input('data');
        $signature_submit = $request->file('data')['signature_submit'];
        $signature_receive = $request->file('data')['signature_receive'];

        for($i=0; $i<count($data['items']); $i++)
        {
            for($j=0; $j<count($data['items'][$i]['items']); $j++)
            {
                //check if the itme is available
                if(ItemSubmitted::where('id', $data['items'][$i]['items'][$j])->count()==0)
                {
                    $status = $this->failedAppData("Item is not available.");

                    $emptyData = collect();

                    $emptyData->appData = $this->prepareAppData($request, $data, $status);

                    return new BaseResource($emptyData);   
                }

                $temp_item = ItemSubmitted::where('id', $data['items'][$i]['items'][$j])->first();

                //check if the key is currently at management side 
                if($temp_item->possessor != "handler")
                {
                    $status = $this->failedAppData("Item is already returned to management.");

                    $emptyData = collect();

                    $emptyData->appData = $this->prepareAppData($request, $data, $status);

                    return new BaseResource($emptyData);
                }

            }
        }

        for($i=0; $i<count($data['items']); $i++)
        {
            $transaction_items = [];
            $item_submitted = ItemSubmitted::whereIn('id', $data['items'][$i]['items'])->groupBy('name')->groupBy('code')->get();
            ItemSubmitted::whereIn('id', $data['items'][$i]['items'])->update(['possessor'=>$data['possessor']]);

            for($j=0;$j<count($item_submitted);$j++)
            {
                $temp_count = ItemSubmitted::whereIn('id', $data['items'][$i]['items'])->where('name', $item_submitted[$j]['name'])->count();

                $transaction_item = (object)[
                    "code"      => $item_submitted[$j]['code'],
                    "name"      => $item_submitted[$j]['name'],
                    "quantity"  => $temp_count,
                ];

                array_push($transaction_items, $transaction_item);
            }

            //store transaction
            $item_submitted_transaction = ItemSubmittedTransaction::create([
                'code'                          => "AI".date("Ymd", strtotime(Carbon::now())).'-'.rand(10000000, 99999999),
                'items'                         => $transaction_items,
                'status'                        => "handover_return",
                'drawing_plan_id'               => $data['items'][$i]['drawing_plan_id'],
                'signature_receive'             => \App\Processors\SaveSignatureProcessor::make($signature_receive)->execute(),
                'signature_submit'              => \App\Processors\SaveSignatureProcessor::make($signature_submit)->execute(),
                'signature_receive_datetime'    => $data['signature_receive_datetime'],
                'signature_submit_datetime'     => $data['signature_submit_datetime'],
                'name_receive'                  => $data['name_receive'],
                'name_submit'                   => $data['name_submit'],
                'internal_remarks'              => isset($data['remarks']) ? $data['remarks'] : "",
                'external_remarks'              => isset($data['external_remarks']) ? $data['external_remarks'] : "",
                'created_by'                    => $user->id,
                'updated_by'                    => $user->id,
            ]); 

        }

        return $this->emptyData($request, []);
    }

}

