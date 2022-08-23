<?php

namespace App\Http\Controllers\Manages\Buyer;

use App\Entity\DrawingPlan;
use App\Entity\DrawingSet;
use App\Entity\JointUnitOwner;
use App\Entity\Project;
use App\Entity\RoleUser;
use App\Entity\User;
use App\Http\Controllers\Controller;
use App\Notifications\CustomerEmail;
use App\Notifications\ExistingCustomerEmail;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class BuyerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('buyers.index');
    }

    public function indexData(){

        $customer = RoleUser::customer()->with('users')->select(['role_user.*']);

        return Datatables::of($customer)
            ->addColumn('email', function ($customer) {
                
                return $label = $customer->users->email;
                
            })
            ->rawColumns(['email'])
            ->addColumn('phone_no', function ($customer) {
                
                return $label = $customer->users->phone_no;
                
            })
            ->rawColumns(['phone_no'])
            ->addColumn('status', function ($customer) {
                
                return $label = $customer->users->verified == 1 ? 'Active' : 'Inactive';
                
            })
            ->addColumn('action', function ($customer) {
                $button = '<a href="'. route('buyer.edit', [$customer->users->id]) .'" data-popup="tooltip" title="'. trans('main.edit') .'" data-placement="top" style="color:black;">
            <i class="fa fa-edit fa-lg"></i>
        </a>';
                // $button .= '<a href="'. route('customer.destroy', [$customer->users->id]) .'" data-popup="tooltip" title="'. trans('main.delete') .'" data-placement="top" class="ajaxDeleteButton" style="color:red;">
            // <i class="fa fa-trash fa-lg"></i>
        // </a>';
        return $button;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $role_user = RoleUser::find(session('role_user_id'));

        $user_id = RoleUser::where('role_id', 7)->select('user_id')->get();

        $email_user = User::whereIn('id', $user_id)->pluck('email')->toArray();


        $primary_project = Project::where('client_id', $role_user->client_id)->with('drawingSet.drawingPlanUnitNoOwner')->get();
        $joint_project = Project::where('client_id', $role_user->client_id)->with('drawingSet.drawingPlanUnitHasOwner')->get();


        return view('buyers.create',compact('email_user', 'primary_project', 'joint_project'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $rules = [
            'salutation'            => 'required',
            'name'                  => 'required|max:255',
            'email'                 => 'required|email|max:255',
            'contact'               => 'nullable|numeric',
            'owner_category.*'      => 'required',
            'owner_project.*'       => 'required',
            'owner_unit.*'          => 'required',
        ];

        $this->validate($request, $rules);

        $primary = [];
        $joint = [];
        foreach ($request->input('owner_category') as $key => $value) {

            switch ($value) {
                case 'primary':
                    $primary[] = $request->input('owner_unit')[$key];
                    break;
                case 'joint':
                    $joint[] = $request->input('owner_unit')[$key];
                    break;
            }
        }

        foreach ($primary as $key => $value) {
            if($plan = DrawingPlan::where('id', $value)->whereNotNull('user_id')->first()){
                return back()->withInput()->with(['warning-message' => 'Please select the correct unit.']);
            }
        }

        foreach ($joint as $key => $value) {

            if($plan = DrawingPlan::where('id', $value)->whereNull('user_id')->first()){
                return back()->withInput()->with(['warning-message' => 'Please select the correct unit.']);
            }

        }

        $result = $this->checkingUser($request);
        $user = $result["user"];
        $type = $result["type"];


        foreach ($primary as $key => $value) {

            $plan = DrawingPlan::find($value);

            $project = $plan->drawingSet->project;
            $plan->user_id = $user->id;
            $plan->save();

            $this->createRoleUser($project, $user, $type);
        }

        foreach ($joint as $key => $value) {

            $plan = DrawingPlan::find($value);
            $project = $plan->drawingSet->project;

            JointUnitOwner::create([
                'drawing_plan_id'   => $value,
                'user_id'           => $user->id,
            ]);

            $this->createRoleUser($project, $user, $type);
        }

        switch ($type) {
            case 'new':
                $user->notify(new CustomerEmail($project->id));
                break;
            case 'old':
                $user->notify(new ExistingCustomerEmail($project->id));
                break;
        }


        return redirect()->route('buyer.index')->with(['success-message'  => 'New Record Successfully added']);
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

        $user = User::find($id);

        $role_user = RoleUser::find(session('role_user_id'));

        $user_id = RoleUser::where('role_id', 7)->select('user_id')->get();

        $email_user = User::whereIn('id', $user_id)->pluck('email')->toArray();

        $drawingPlan = DrawingPlan::where('user_id', $id)->first();

        $primary_project = Project::where('client_id', $role_user->client_id)->with('drawingSet.drawingPlanUnitNoOwner')->get();
        $joint_project = Project::where('client_id', $role_user->client_id)->with('drawingSet.drawingPlanUnitHasOwner')->get();

        return view('buyers.edit',compact('user', 'email_user', 'primary_project', 'joint_project', 'drawingPlan'));
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
        
        $user = User::find($id);

        $user->update([
            'salutation'        => $request->input('salutation'),
            'buyer_id'          => $request->input('buyer_id'),
            'name'              => $request->input('name'),
            'ic_no'             => $request->input('ic_no'),
            'passport_no'       => $request->input('passport_no'),
            'comp_reg_no'       => $request->input('company_reg_no'),
            'phone_no'          => $request->input('phone_no'),
            'house_no'          => $request->input('house_no'),
            'office_no'         => $request->input('office_no'),
            'mailing_address'   => $request->input('mail_address'),
        ]);

        return redirect()->route('buyer.index')->with(['success-message'  => 'Record Successfully updated']);

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

    function checkingUser($request){

        $user = User::where('email', $request->input('email'))->first();

        if($user){
            ##UPDATE USER DETAILS
            $user->salutation = $request->input('salutation');
            $user->buyer_id = $request->input('buyer_id');
            $user->name = $request->input('name');
            $user->ic_no = $request->input('ic_no');
            $user->passport_no = $request->input('passport_no');
            $user->comp_reg_no = $request->input('company_reg_no');
            $user->phone_no = $request->input('phone_no');
            $user->house_no = $request->input('house_no');
            $user->office_no = $request->input('office_no');
            $user->mailing_address = $request->input('mail_address');
            $user->save();

            $type = 'old';
        }else{

            $type = 'new';


            $user = User::create([
                'salutation'        => $request->input('salutation'),
                'buyer_id'          => $request->input('buyer_id'),
                'name'              => $request->input('name'),
                'email'             =>$request->input('email'),
                'password'          => bcrypt("qms1234"),
                'ic_no'             => $request->input('ic_no'),
                'passport_no'       => $request->input('passport_no'),
                'comp_reg_no'       => $request->input('company_reg_no'),
                'phone_no'          => $request->input('phone_no'),
                'house_no'          => $request->input('house_no'),
                'office_no'         => $request->input('office_no'),
                'mailing_address'   => $request->input('mail_address'),
            ]);

        }

        return $result = ['user' => $user , 'type' => $type];

    }

    function createRoleUser($project, $user, $type){

        if(!RoleUser::where('user_id', $user->id)->where('role_id', 7)->where('project_id', $project->id)->first()){

            RoleUser::create([
                'user_id'       => $user->id,
                'role_id'       => '7', #customer role
                'project_id'    => $project->id,
                'client_id'     => $project->client_id, 
            ]);
        }


    }
}
