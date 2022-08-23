<?php

namespace App\Http\Controllers\Api\v1\ThirdParty\Users;

use Validator;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Project;
use App\Entity\RoleUser;
use App\Entity\DrawingPlan;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\v1\BaseApiController;

class ManageUserController extends BaseApiController
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
     *     path="/third-party/buyers/store",
     *     summary="Third Party Create Buyer",
     *     method="post",
     *     tags={"Buyer (Third Party)"},
     *     description="This API will create buyer",
     *     operationId="store",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                   property="buyer",
     *                   type="object",
     *                      @SWG\Property(property="name",type="string",example="user1"),
     *                      @SWG\Property(property="email",type="string",example="user@uemhub.com"),
     *                      @SWG\Property(property="contact", type="string",example="0121234567"),
     *                      @SWG\Property(property="buyer_id", type="string",example="112233"),
     *                      @SWG\Property(property="project_id",
     *                           type="array",
     *                              @SWG\Items(type="string"),
     *                           example={"e31dc2ca-9d5e-a2ea-7e58-4c5fc0e20e1f",
     *                              "108804fa-d8bd-9680-58db-5302f2fd5fd0"}
     *                       ),
     *                      @SWG\Property(property="unit_id",
     *                           type="array",
     *                              @SWG\Items(type="string"),
     *                           example={"e31dc2ca-9d5e-a2ea-7e58-4c5fc0e20e1f",
     *                              "108804fa-d8bd-9680-58db-5302f2fd5fd0"}
     *                       )
     *               ),
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
            'email'        => 'required|unique:users',
            'buyer_id'     => 'required|unique:users',
            'project_id.*' => 'exists:projects,project_id',
            'unit_id.*'    => 'exists:drawing_plans,unit_id',
        ];

        $validator = Validator::make($request->input('buyer'), $rules, [
            'email.unique'        => 'The email :input has been taken.',
            'buyer_id.unique'     => 'The buyer_id :input has been taken.',
            'project_id.*.exists' => 'The project_id :input does not exist.',
            'unit_id.*.exists'    => 'The unit_id :input does not exist.',
        ]);

        $data = $this->data;

        if ($validator->fails()) {

            return third_party_response('999999', 'failed', $validator->errors()->first(), []);
        }

        $role = Role::find(7);
        $customer = User::create([
            'name'         => $request->input('buyer.name'),
            'email'        => $request->input('buyer.email'),
            'password'     => bcrypt('qms1234'),
            'contact'      => $request->input('buyer.contact'),
            'current_role' => '7',
            'buyer_id'     => $request->input('buyer.buyer_id'),
        ]);

        if (count($request->input('buyer.project_id')) > 0) {
            foreach ($request->input('buyer.project_id') as $key => $project_id) {
                $project = Project::where('project_id', $project_id)->first();
                $client_id = $project->client_id;

                $customer->roles()->attach($role, [
                    'project_id' => $project->id,
                    'client_id'  => $client_id,
                ]);
            }
        } else {
            $customer->roles()->attach($role);
        }

        if (count($request->input('buyer.unit_id')) > 0) {
            foreach ($request->input('buyer.unit_id') as $key => $unit_id) {
                DrawingPlan::where('unit_id', $unit_id)
                    ->update(['user_id' => $customer->id]);
            }
        }

        return third_party_response('', 'success', '', []);
    }

    /**
     * @SWG\Post(
     *     path="/third-party/buyers",
     *     summary="Third Party Get Buyer",
     *     method="post",
     *     tags={"Buyer (Third Party)"},
     *     description="This API will get buyer information",
     *     operationId="show",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *                  @SWG\Property(property="buyer_id", type="string",example="112233"),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function show(Request $request)
    {
        $rules = [
            'buyer_id'     => 'required|exists:users,buyer_id',
            'project_id.*' => 'exists:projects,project_id',
        ];

        $validator = Validator::make($request->input(), $rules, [
            'buyer_id.exists'     => 'The buyer_id :input does not exist.',
            'project_id.*.exists' => 'The project_id :input does not exist.',
        ]);

        $data = $this->data;

        if ($validator->fails()) {

            return third_party_response('999999', 'failed', $validator->errors()->first(), []);
        }

        $customer = User::where('buyer_id', $request->input('buyer_id'))->first();

        if (!$customer) {
            return third_party_response('999999', 'failed', 'The buyer_id ' . $request->input('buyer_id') . ' does not exist.', []);
        }

        $project_ids = [];

        $units = DrawingPlan::where('user_id', $customer->id);

        if (count($request->input('project_id')) > 0) {
            $project_ids = Project::whereIn('project_id', $request->input('project_id'))->pluck('id')->toArray();
            $units = $units->whereHas('drawingSet', function ($q) use ($project_ids) {
                $q->whereIn('project_id', $project_ids);
            });
        }

        $units = $units->whereNotNull('drawing_plans.block')
            ->whereNotNull('drawing_plans.level')
            ->whereNotNull('drawing_plans.unit')
            ->whereIn('types', ['unit'])
            ->with('itemSubmitted')
            ->with('location.issues')
            ->get();

        $customer->units = $units;

        return third_party_response('', 'success', '', $customer);
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
     * @SWG\Post(
     *     path="/third-party/buyers/update",
     *     summary="Third Party Update Buyer",
     *     method="post",
     *     tags={"Buyer (Third Party)"},
     *     description="This API will update buyer information",
     *     operationId="update",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *                  @SWG\Property(property="buyer_id", type="string",example="112233"),
     *                  @SWG\Property(property="name",type="string",example="user1"),
     *                  @SWG\Property(property="email",type="string",example="user@uemhub.com"),
     *                  @SWG\Property(property="contact", type="string",example="0121234567"),
     *                  @SWG\Property(property="project_id",
     *                          type="array",
     *                          @SWG\Items(type="string"),
     *                          example={"e31dc2ca-9d5e-a2ea-7e58-4c5fc0e20e1f",
     *                              "108804fa-d8bd-9680-58db-5302f2fd5fd0"}
     *                       ),
     *                      @SWG\Property(property="unit_id",
     *                           type="array",
     *                              @SWG\Items(type="string"),
     *                           example={"e31dc2ca-9d5e-a2ea-7e58-4c5fc0e20e1f",
     *                              "108804fa-d8bd-9680-58db-5302f2fd5fd0"}
     *                       )
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function update(Request $request)
    {
        $rules = [
            'buyer_id'     => 'required|exists:users,buyer_id',
            'project_id.*' => 'exists:projects,project_id',
            'unit_id.*'    => 'exists:drawing_plans,unit_id',
        ];

        $validator = Validator::make($request->input(), $rules, [
            'buyer_id.exists'     => 'The buyer_id :input does not exist.',
            'project_id.*.exists' => 'The project_id :input does not exist.',
            'unit_id.*.exists'    => 'The unit_id :input does not exist.',
        ]);

        $data = $this->data;

        if ($validator->fails()) {

            return third_party_response('999999', 'failed', $validator->errors()->first(), []);
        }

        $role = Role::find(7);
        $customer = User::where('buyer_id', $request->input('buyer_id'))->first();

        if (!$customer) {
            return third_party_response('999999', 'failed', 'The buyer_id ' . $request->input('buyer_id') . ' does not exist.', []);
        }

        $customer->update([
            'name'    => $request->input('name'),
            'email'   => $request->input('email'),
            'contact' => $request->input('contact'),
        ]);

        $project_ids = [];

        if (count($request->input('project_id')) > 0) {
            $project_ids = Project::whereIn('project_id', $request->input('project_id'))->pluck('id')->toArray();

            foreach ($request->input('project_id') as $key => $project_id) {
                $project = Project::where('project_id', $project_id)->first();

                if (RoleUser::where('role_id', 7)
                    ->where('client_id', $project->client_id)
                    ->where('project_id', $project->id)
                    ->where('user_id', $customer->id)
                    ->doesntExist()) {
                    $customer->roles()->attach($role, [
                        'project_id' => $project->id,
                        'client_id'  => $project->client_id,
                    ]);
                }

            }
        }

        $unlinked_projects = $customer->projectByRole()
            ->whereNotIn('role_user.project_id', $project_ids)
            ->where('role_user.role_id', 7)
            ->where('role_user.user_id', $customer->id)
            ->pluck('role_user.project_id')->toArray();

        if (count($unlinked_projects) > 0) {
            $customer->projectByRole()->detach($unlinked_projects);
        }

        if (count($request->input('unit_id')) > 0) {
            $drawing_plan_ids = DrawingPlan::whereIn('unit_id', $request->input('unit_id'))->pluck('id')->toArray();

            foreach ($request->input('unit_id') as $key => $unit_id) {

                if (DrawingPlan::where('unit_id', $unit_id)
                    ->where('user_id', $customer->id)
                    ->doesntExist()) {
                    DrawingPlan::where('unit_id', $unit_id)
                        ->update(['user_id' => $customer->id]);
                }

            }
        }

        DrawingPlan::
            whereNotIn('unit_id', $request->get('unit_id', []))
            ->where('user_id', $customer->id)
            ->update(['user_id' => null]);

        return third_party_response('', 'success', '', []);
    }

    /**
     * @SWG\Post(
     *     path="/third-party/buyers/delete",
     *     summary="Third Party Delete Buyer",
     *     method="post",
     *     tags={"Buyer (Third Party)"},
     *     description="This API will delete buyer.",
     *     operationId="destroy",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *                @SWG\Property(property="buyer_id",type="string",example="112233"),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function destroy(Request $request)
    {
        $rules = [
            'buyer_id' => 'required|exists:users,buyer_id',
        ];

        $validator = Validator::make($request->input(), $rules, [
            'buyer_id.exists' => 'The buyer_id :input does not exist.',
        ]);

        $data = $this->data;

        if ($validator->fails()) {

            return third_party_response('999999', 'failed', $validator->errors()->first(), []);
        }

        $role = Role::find(7);
        $customer = User::where('buyer_id', $request->input('buyer_id'))->first();

        if (!$customer) {
            return third_party_response('999999', 'failed', 'The buyer_id ' . $request->input('buyer_id') . ' does not exist.', []);
        }

        $email = $customer->email . "_delete_" . NOW();
        $customer->roles()->where('role_user.role_id', 7)->delete();

        DrawingPlan::where('user_id', $customer->id)
            ->update(['user_id' => null]);

        $customer->update([
            'email' => $email,
        ]);

        $customer->delete();

        return third_party_response('', 'success', '', []);
    }

    /**
     * @SWG\Post(
     *     path="/third-party/buyers/batch-store",
     *     summary="Third Party Batch Create Buyer",
     *     method="post",
     *     tags={"Buyer (Third Party)"},
     *     description="This API will create buyer in batch",
     *     operationId="batchStore",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         in="body",
     *         name="body",
     *         type="object",
     *         @SWG\Schema(
     *              @SWG\Property(property="buyers",
     *                  type="array",
     *                  @SWG\Items(
     *                      type="object",
     *                      @SWG\Property(property="name",type="string",example="user1"),
     *                      @SWG\Property(property="email",type="string",example="user@uemhub.com"),
     *                      @SWG\Property(property="contact", type="string",example="0121234567"),
     *                      @SWG\Property(property="buyer_id", type="string",example="112233"),
     *                      @SWG\Property(property="project_id",
     *                           type="array",
     *                              @SWG\Items(type="string"),
     *                           example={"e31dc2ca-9d5e-a2ea-7e58-4c5fc0e20e1f",
     *                              "108804fa-d8bd-9680-58db-5302f2fd5fd0"}
     *                       ),
     *                      @SWG\Property(property="unit_id",
     *                           type="array",
     *                              @SWG\Items(type="string"),
     *                           example={"e31dc2ca-9d5e-a2ea-7e58-4c5fc0e20e1f",
     *                              "108804fa-d8bd-9680-58db-5302f2fd5fd0"}
     *                       )
     *                  ),
     *               ),
     *         ),
     *      ),
     *     @SWG\Parameter(in="query",name="token",required=true,type="string"),
     *     @SWG\Response(response="200", description="")
     * )
     * @param Request $request
     */
    public function batchStore(Request $request)
    {

        $rules = [
            'buyers.*.email'        => 'unique:users',
            'buyers.*.buyer_id'     => 'unique:users',
            'buyers.*.project_id.*' => 'exists:projects,project_id',
            'buyers.*.unit_id.*'    => 'exists:drawing_plans,unit_id',
        ];

        $validator = Validator::make($request->input(), $rules, [
            'buyers.*.email.unique'        => 'The email :input has been taken.',
            'buyers.*.buyer_id.unique'     => 'The buyer_id :input has been taken.',
            'buyers.*.project_id.*.exists' => 'The project_id :input does not exist.',
            'buyers.*.unit_id.*.exists'    => 'The unit_id :input does not exist.',
        ]);

        $data = $this->data;

        if ($validator->fails()) {

            return third_party_response('999999', 'failed', $validator->errors()->first(), []);
        }

        $role = Role::find(7);

        foreach ($request->input('buyers') as $key => $value) {
            $customer = User::create([
                'name'         => $value['name'],
                'email'        => $value['email'],
                'password'     => bcrypt('qms1234'),
                'contact'      => $value['contact'],
                'current_role' => '7',
                'buyer_id'     => $value['buyer_id'],
            ]);

            if (count($value['project_id']) > 0) {
                foreach ($value['project_id'] as $key => $project_id) {
                    $project = Project::where('project_id', $project_id)->first();
                    $client_id = $project->client_id;

                    $customer->roles()->attach($role, [
                        'project_id' => $project->id,
                        'client_id'  => $client_id,
                    ]);
                }
            } else {
                $customer->roles()->attach($role);
            }

            if (count($value['unit_id']) > 0) {
                foreach ($value['unit_id'] as $key => $unit_id) {
                    DrawingPlan::where('unit_id', $unit_id)
                        ->update(['user_id' => $customer->id]);
                }
            }
        }

        return third_party_response('', 'success', '', []);

    }
}
