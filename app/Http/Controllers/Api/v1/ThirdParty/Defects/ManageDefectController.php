<?php

namespace App\Http\Controllers\Api\v1\ThirdParty\Defects;

use Helper;
use Validator;
use Carbon\Carbon;
use App\Entity\User;
use App\Entity\Issue;
use App\Entity\History;
use App\Entity\DrawingSet;
use App\Entity\DrawingPlan;
use App\Entity\SettingIssue;
use Illuminate\Http\Request;
use App\Entity\LocationPoint;
use App\Http\Controllers\Api\v1\BaseApiController;

class ManageDefectController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * @SWG\Post(
     *     path="/third-party/defects/store",
     *     summary="Third Party Submit Defect",
     *     method="post",
     *     tags={"Defect (Third Party)"},
     *     description="This API will submit defect",
     *     operationId="store",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(property="buyer_id",type="string",example="112233"),
     *              @SWG\Property(property="unit_id",type="string",example="123"),
     *              @SWG\Property(property="location_id",type="string",example="47"),
     *              @SWG\Property(property="issue_setting_id",type="string",example="5"),
     *              @SWG\Property(property="comment",type="string",example="crack in the wall"),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function store(Request $request)
    {
        $rules = [
            'buyer_id'         => 'required|exists:users',
            'unit_id'          => 'required|exists:drawing_plans,unit_id',
            'location_id'      => 'required|exists:locations,id',
            'issue_setting_id' => 'required|exists:setting_issues,id',
        ];

        $validator = Validator::make($request->input(), $rules, [
            'buyer_id.exists'         => 'The buyer_id :input does not exist.',
            'unit_id.exists'          => 'The unit_id :input does not exist.',
            'location_id.exists'      => 'The location_id :input does not exist.',
            'issue_setting_id.exists' => 'The issue_setting_id :input does not exist.',
        ]);

        $data = $this->data;

        if ($validator->fails()) {

            return third_party_response('999999', 'failed', $validator->errors()->first(), []);
        }

        $user = User::where('buyer_id', $request->input('buyer_id'))->first();

        if (!$user) {
            return third_party_response('999999', 'failed', 'The buyer_id ' . $request->input('buyer_id') . ' does not exist.', []);
        }

        $unit = DrawingPlan::where('unit_id', $request->input('unit_id'))->first();

        if ($unit->user_id != $user->id) {

            $drawing_set_ids = DrawingSet::where('project_id', $unit->drawingSet->project_id)->pluck('id')->toArray();

            if (DrawingPlan::
                whereIn('drawing_set_id', $drawing_set_ids)
                ->where('types', 'common')
                ->doesntExist()) {

                return third_party_response('999999', 'failed', 'You are not allowed to do this action.', []);
            }
        }

        $issue_setting = SettingIssue::find($request->input('issue_setting_id'));
        $remark = $request->filled('comment') ? $request->input('comment') : "Owner created an issue.";
        $location = LocationPoint::find($request->input('location_id'));

        $issue = Issue::create([

            'location_id'         => $location->id,
            'setting_category_id' => $issue_setting->category_id,
            'setting_type_id'     => $issue_setting->type_id,
            'setting_issue_id'    => $issue_setting->id,
            'position_x'          => explode(',', $location->points)[0],
            'position_y'          => explode(',', $location->points)[1],
            'remarks'             => $remark,
            'status_id'           => 1,
            'created_by'          => $user->id,
        ]);

        $now = Carbon::now('Asia/Kuala_Lumpur');
        $unique_ref = Helper::generateIssueReferenceByLocationID($request->input('location_id'));
        $issue->forcefill(['reference' => $unique_ref])->save();

        $history = History::create([
            'user_id'       => $user->id,
            'issue_id'      => $issue->id,
            'status_id'     => $issue->status_id,
            'remarks'       => $remark,
            'customer_view' => 1,
        ]);

        return third_party_response('', 'success', '', [
            'issue_id' => $issue->id,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
