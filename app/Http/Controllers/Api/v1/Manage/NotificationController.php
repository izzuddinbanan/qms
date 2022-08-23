<?php

namespace App\Http\Controllers\Api\v1\Manage;

use Validator;
use App\Supports\AppData;
use App\Entity\Notification;
use App\Entity\Issue;
use Illuminate\Http\Request;
use App\Http\Resources\BaseResource;
use App\Http\Resources\NotificationCollection;
use App\Http\Resources\IssueResource;
use App\Http\Controllers\Api\v1\BaseApiController;

class NotificationController extends BaseApiController
{

    use AppData;
    
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @SWG\Post(
     *     path="/notification",
     *     summary="List of all notification -- notification not ready only",
     *     method="POST",
     *     tags={"Notification"},
     *     description="Use this API to retrieve all notification/history.",
     *     operationId="notification",
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
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     * @param $string
     */
    public function notification(Request $request)
    {
        $user = $this->user;
        $data = $this->data;

        $last_month = \Carbon\Carbon::today()->subDays(30);
        // $notification = $user->notification()->orderBy('created_at', 'desc')->where('created_at', '>=', $last_month)->get();
        $notification = $user->notification()->where('read_status_id', 0)->where('type', '!=', 'reminder')->orderBy('created_at', 'desc')->get();

        if($notification->isEmpty()) {
            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data);

            return new BaseResource($emptyData);
        }

        array_push($data, $notification);

        $notification->appData = $this->prepareAppData($request, $data);

        // return $notification;
        return (new NotificationCollection($notification))->additional(['AppData' => $this->appData]);
    }


    /**
     * @SWG\Post(
     *     path="/notification/view",
     *     summary="To view notification details",
     *     method="post",
     *     tags={"Notification"},
     *     description="This Api will get notification details. ",
     *     operationId="viewNotification",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="data",
     *                   type="object",
     *                      @SWG\Property(property="issue_id",type="string",example="1"),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function viewNotification(Request $request)
    {
        $data = $this->data;
        $user = $this->user;

        $rules = [
            'issue_id'         => 'required',
        ];

        $message = [
        ];

        $validator = Validator::make($request->input('data'), $rules, $message);


        if ($validator->fails()) {

            $status = $this->failedAppData($validator->errors()->first());

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }


        if(!$issue = Issue::find($request->input('data.issue_id'))){
            $status = $this->failedAppData('Issue not found');

            $emptyData = collect();
            $emptyData->appData = $this->prepareAppData($request, $data, $status);

            return new BaseResource($emptyData);
        }

        Notification::where('issue_id', $request->input('data.issue_id'))->where('user_id', $user->id)->update(['read_status_id' => 1]);
        
        array_push($data, $issue);

        $issue->appData = $this->prepareAppData($request, $data);
        return new IssueResource($issue);

    }


    /**
     * @SWG\Post(
     *     path="/notification/clear",
     *     summary="To clear all notification user",
     *     method="POST",
     *     tags={"Notification"},
     *     description="Use this API to clear all notification/history.",
     *     operationId="clearNotification",
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
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     * @param $string
     */
    public function clearNotification(Request $request)
    {
        $user = $this->user;
        $data = $this->data;

        
        $emptyData = collect();
        $emptyData->appData = $this->prepareAppData($request, $data);


        Notification::where('user_id', $user->id)->update(['read_status_id' =>  1]);
        return new BaseResource($emptyData);


    }




}
