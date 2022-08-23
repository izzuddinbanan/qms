<?php

namespace App\Http\Controllers;

use Session;
use Response;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Client;
use App\Entity\Project;
use App\Entity\RoleUser;
use App\Entity\DrawingPlan;
use Illuminate\Http\Request;
use App\Imports\CustomerImport;
use App\Exports\CustomerExport;
use App\Exports\CustomerSampleExport;
use Yajra\Datatables\Datatables;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\CustomerEmail;
use App\Notifications\ExistingCustomerEmail;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('customer.index', compact('customer'));
    }

    public function indexData(){
        $session_id = session('project_id');

        if(Session::has('project_id')){
            Session::put('project_id', session('project_id') );
            $session_id = session('project_id');
        }

        $customer = RoleUser::customer()->project($session_id)->with('users')->select(['role_user.*']);

        return Datatables::of($customer)
            ->addColumn('email', function ($customer) {
                
                return $label = $customer->users->email;
                
            })
            ->rawColumns(['email'])
            ->addColumn('contact', function ($customer) {
                
                return $label = $customer->users->contact;
                
            })
            ->rawColumns(['contact'])
            ->addColumn('status', function ($customer) {
                
                return $label = $customer->users->verified == 1 ? 'Active' : 'Inactive';
                
            })
            ->addColumn('action', function ($customer) {
                $button = '<a href="'. route('customer.show', [$customer->users->id]) .'" data-popup="tooltip" title="'. trans('main.show') .'" data-placement="top" style="color:black;">
            <i class="fa fa-eye fa-lg"></i>
        </a>';
                $button .= '<a href="'. route('customer.edit', [$customer->users->id]) .'" data-popup="tooltip" title="'. trans('main.edit') .'" data-placement="top" style="color:black;">
            <i class="fa fa-edit fa-lg"></i>
        </a>';
                $button .= '<a href="'. route('customer.destroy', [$customer->users->id]) .'" data-popup="tooltip" title="'. trans('main.delete') .'" data-placement="top" class="ajaxDeleteButton" style="color:red;">
            <i class="fa fa-trash fa-lg"></i>
        </a>';
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
        //
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
            'name'      => 'required|max:255',
            'email'     => 'required|email|max:255',
            'contact'   => 'nullable|numeric',
            'unit'      => 'required',
        ];

        $this->validate($request, $rules);

        $error = [
            'count'   => 0,
            'msg'    => [],
        ];
        $project_id = session('project_id');
        if(Session::has('project_id')){
            Session::put('project_id', session('project_id') );
            $project_id = session('project_id');
        }
        $project_name = Project::find($project_id)->name;

        //validate if email is in use
        if(User::where('email', $request->input('email'))->count()>0)
        {
            $temp_user_id = User::where('email',$request->input('email'))->first()->id;
            //if user is a customer && inside the same project
            if(RoleUser::where('user_id', $temp_user_id)->customer()->count()>0)
            {
                //if user is inside the same project
                if(RoleUser::where('user_id', $temp_user_id)->where('project_id', $project_id)->count()>0)
                {
                    $error['count']+=1;
                    $line_error = 1;
                    $error_msg = "Email \"" . $request->input('email') . "\" is already available as customer of ". $project_name . ".";
                    
                    array_push($error['msg'], $error_msg);
                }
            }
            //user is registered as role other than customer
            else
            {
                //if the user have role
                if(RoleUser::where('user_id', $temp_user_id)->count()>0)
                {
                    $user_role_id = RoleUser::where('user_id', $temp_user_id)->first()->role_id;
                    $role_name = Role::find($user_role_id)->display_name;
                    $error['count']+=1;
                    $line_error = 1;
                    $error_msg = "Email \"" . $file[0][$i]['email'] . "\" is already registered as ". $role_name . ". Please try with other email.";
                    
                    array_push($error['msg'], $error_msg);
                }
            }
        }

        //validate unit available 
            if(DrawingPlan::where('name', $request->input('unit'))->count()>0)
            {
                //validate if the unit is owned by other customer
                $drawing_plan = DrawingPlan::where('name', $request->input('unit'))->first();
                if(isset($drawing_plan->user_id) && $drawing_plan->user_id!=null && $drawing_plan->user_id!="")
                {
                    $error['count']+=1;
                    $line_error = 1;
                    $error_msg = "Unit \"" . $request->input('unit') . "\" is already registered to customer.";
                    
                    array_push($error['msg'], $error_msg);
                }
            }
            //unit not available
            else{
                $error['count']+=1;
                $line_error = 1;
                $error_msg = "Unit \"" . $request->input('unit') . "\" not found.";
                
                array_push($error['msg'], $error_msg);
            }


        if($error['count']>0)
        {
            //return failed records
            return back()->with(['upload_status' => "failed", 'error_counts' => $error['count'], 'error_msg' => $error['msg']]);
        }
        else{
            $record=[
                "name"      => $request->input('name'),
                "email"     => $request->input('email'),
                "contact"   => $request->input('contact'),
                "unit"      => $request->input('unit'),
            ];

            //customer exist in database
            if(User::where('email', $request->input('email'))->count()>0)
            {
                $this->store_existing_customer($record);
            }
            //customer not exist
            else
            {
                $this->store_customer($record);
            }
            return back()->with(['success-message' => 'Customer successfully added.', 'upload_status'=>'success']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $project_id = session('project_id');
        if(Session::has('project_id')){
            Session::put('project_id', session('project_id') );
            $project_id = session('project_id');
        }

        if(User::where('id', $id)->count()==0){
            return redirect()->route('customer.index')->withErrors(trans('main.record-not-found'));
        }
        else
        {
            if(RoleUser::where('user_id', $id)->where('project_id', $project_id)->customer()->count()>0)
            {
               $customer = User::where('id', $id)->first();
            } 
            else
            {
                return redirect()->route('customer.index')->withErrors(trans('main.record-not-found'));
            }
        }

        
        $unit = DrawingPlan::where('user_id', $id)
            ->whereNull('deleted_at')
            ->whereNotNull('block')
            ->whereNotNull('level')
            ->whereNotNull('unit')
            ->whereIn('types', ['unit'])
            ->select(['id', 'block', 'level', 'unit'])
            ->get();
        return view('customer.show', compact('customer', 'unit', 'project_id')); 
    }

    /**
     * Ajax function to get customer table listing.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getListing(Request $request){
        $param = $request->all();
        $project = Project::find(session('project_id'));
        // $param['type'] = $type = 'unit';
        // $param['project_id'] = session('project_id');
        $current_page = $request->get('page');

        // $issues = (new FilterService())->generateUnitSummaryQuery($param)->get();
        //listing == issues
        $listing = DrawingPlan::where('user_id', $param['customer_id'])
                    ->join('drawing_sets', 'drawing_sets.id', '=', 'drawing_plans.drawing_set_id')
                    ->join('projects', 'drawing_sets.project_id', 'projects.id')
                    ->where('drawing_sets.project_id', $param['project_id'])
                    ->whereNotNull('drawing_plans.block')
                    ->whereNotNull('drawing_plans.level')
                    ->whereNotNull('drawing_plans.unit')
                    ->whereIn('types', ['unit'])
                    ->select(['drawing_plans.id', 'drawing_plans.block', 'drawing_plans.level', 'drawing_plans.unit'])
                    ->get();

        $total = $listing->count();
        $per_page = 10;

        $listing = $listing->slice($per_page * ($current_page - 1))->take($per_page);
        $listing = new LengthAwarePaginator($listing, $total, $per_page);
        
        return view('customer.listing', compact('listing', 'project'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $project_id = session('project_id');
        if(Session::has('project_id')){
            Session::put('project_id', session('project_id') );
            $project_id = session('project_id');
        }

        if(User::where('id', $id)->count()==0){
            return redirect()->route('customer.index')->withErrors(trans('main.record-not-found'));
        }
        else
        {
            if(RoleUser::where('user_id', $id)->where('project_id', $project_id)->customer()->count()>0)
            {
               $customer = User::where('id', $id)->first();
            } 
            else
            {
                return redirect()->route('customer.index')->withErrors(trans('main.record-not-found'));
            }
        }
        return view('customer.edit', compact('customer')); 

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
        try {
            $rules = [
                'name'      => 'required|max:255',
                'email'     => 'required|email|max:255',
                'contact'   => 'nullable|numeric',
            ];

            $this->validate($request, $rules);

            if(User::where('id', $id)->count()>0)
            {
                $project_id = session('project_id');
                if(Session::has('project_id')){
                    Session::put('project_id', session('project_id') );
                    $project_id = session('project_id');
                }

                //validate session (pending)
                if(RoleUser::where('user_id', $id)
                            ->where('project_id', $project_id)
                            ->customer()
                            ->count()>0)
                {
                    // dd(User::whereEmail($request->input('email'))->first());
                    if(User::where('email', $request->input('email'))->count()>0)
                    {
                        // dd("email in use");
                        return redirect()->route('customer.index')->withErrors(trans('main.email-in-used'));exit();
                    }
                    else{
                        // dd("email not in use");
                        $user = User::find($id);
                        $user->name = $request->input('name');
                        $user->email = $request->input('email');
                        $user->contact = $request->input('contact');
                        $user->save(); 
                    }
                    
                }
                else
                {
                    return redirect()->route('customer.index')->withErrors(trans('main.record-not-found'));
                }
            }
            else
            {
                return redirect()->route('customer.index')->withErrors(trans('main.record-not-found'));
            }

        } catch (ValidationException $e) {
            return redirect(route('customer.edit', [$customer->id]))
                ->withErrors($e->getErrors())
                ->withInput();
        }
        return redirect(route('customer.index'))
            ->withSuccess(trans('main.success-update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {

            if (User::where('id', $id)->count()>0) {

                $customer = User::find($id);

                $customer_plan = DrawingPlan::where('user_id', $id)->get();
                if(count($customer_plan)>0)
                {
                    foreach($customer_plan as $cp)
                    {
                        $cp->user_id = null;
                        $cp->save();
                    }
                }

                $customer->update([
                    'email' => $customer->email . "_delete_" . NOW(),
                ]);
                
                $customer->delete();
                
                return response()->json(['status' => 'ok']);
            }

        }
        return Response::json(['status' => 'fail']);
    }

    /**
     * Export customer as PDF.
     *
     * @param 
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        return Excel::download(new CustomerExport, 'customer.xlsx');
    }

    /**
     * Export sample customer PDF.
     *
     * @param 
     * @return \Illuminate\Http\Response
     */
    public function export_sample()
    {
        return Excel::download(new CustomerSampleExport, 'sample.xlsx');
    }

    /**
     * Import customer information from PDF file.
     *
     * @param 
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $rules = [
            'customer_import' => 'file|mimes:xlsx',
        ];

        $this->validate($request, $rules);
        
        $file = \Excel::toArray(new CustomerImport, request()->file('customer_import'));
        $customer_validation = $this->validate_customer($file);

        if($customer_validation['count']>0)
        {
            //return failed records
            return back()->with(['upload_status' => "failed", 'error_counts' => $customer_validation['count'], 'error_msg' => $customer_validation['msg']]);
        }
        else{
            //store data
            for($i=0;$i<count($file[0]);$i++)
            {
                //customer exist in database
                if(User::where('email', $file[0][$i]['email'])->count()>0)
                {
                    $this->store_existing_customer($file[0][$i]);
                }
                //customer not exist
                else
                {
                    $this->store_customer($file[0][$i]);
                }
            }
            return back()->with(['success-message' => 'Customer successfully added.', 'upload_status'=>'success']);
        }
    }

    public function validate_customer($file)
    {
        $error = [
            'count'   => 0,
            'msg'    => [],
        ];

        $mail_array=[];

        $project_id = session('project_id');
        if(Session::has('project_id')){
            Session::put('project_id', session('project_id') );
            $project_id = session('project_id');
        }
        $project = Project::find($project_id);
        $project_name = $project->name;
        $client_id = Client::find($project->client_id);

        for($i=0;$i<count($file[0]);$i++)
        {
            if(count($mail_array)==0)
            {
                array_push($mail_array, $file[0][$i]['email']);
            }
            else{
                for($j=0;$j<count($mail_array);$j++)
                {
                    //have duplicate email inside the excel file
                    if($file[0][$i]['email'] == $mail_array[$j]){
                        $first_duplicate_record = $j+1;
                        $second_duplicate_record = $i+1;

                        $error['count']+=1;
                        $line_error = $i+1;
                        $error_msg = "Email \"" . $file[0][$i]['email'] . "\" in line " . $first_duplicate_record . " is repeating in line ". $second_duplicate_record . ".";
                        
                        array_push($error['msg'], $error_msg);
                    }
                    else
                    {
                        array_push($mail_array, $file[0][$i]['email']);
                    }
                }
            }
            
            //validate if email is in use
            if(User::where('email', $file[0][$i]['email'])->count()>0)
            {
                $temp_user_id = User::where('email',$file[0][$i]['email'])->first()->id;
                //if user is a customer && inside the same project
                if(RoleUser::where('user_id', $temp_user_id)->customer()->count()>0)
                {
                    //if user is inside the same project
                    if(RoleUser::where('user_id', $temp_user_id)->where('project_id', $project_id)->count()>0)
                    {
                        $error['count']+=1;
                        $line_error = $i+1;
                        $error_msg = "Email \"" . $file[0][$i]['email'] . "\" in line " . $line_error . " is already available as customer of ". $project_name . ".";
                        
                        array_push($error['msg'], $error_msg);
                    }
                }
                //uses is registered as role other than customer
                else
                {
                    $user_role_id = RoleUser::where('user_id', $temp_user_id)->first()->role_id;
                    $role_name = Role::find($user_role_id)->display_name;
                    $error['count']+=1;
                    $line_error = $i+1;
                    $error_msg = "Email \"" . $file[0][$i]['email'] . "\" in line " . $line_error . " is already registered as ". $role_name . ". Please try with other email.";
                    
                    array_push($error['msg'], $error_msg);
                }
            }

            //validate unit available 
            if(DrawingPlan::where('name', $file[0][$i]['unit'])->count()>0)
            {
                //validate if the unit is owned by other customer
                $drawing_plan = DrawingPlan::where('name', $file[0][$i]['unit'])->first();
                if(isset($drawing_plan->user_id) && $drawing_plan->user_id!=null && $drawing_plan->user_id!="")
                {
                    $error['count']+=1;
                    $line_error = $i+1;
                    $error_msg = "Unit \"" . $file[0][$i]['unit'] . "\" in line " . $line_error . " is already registered to customer.";
                    
                    array_push($error['msg'], $error_msg);
                }
            }
            //unit not available
            else{
                $error['count']+=1;
                $line_error = $i+1;
                $error_msg = "Unit \"" . $file[0][$i]['unit'] . "\" in line " . $line_error . " not found.";
                
                array_push($error['msg'], $error_msg);
            }
        }
        return $error;
    }

    public function store_customer($customer)
    {
        $project_id = session('project_id');
        if(Session::has('project_id')){
            Session::put('project_id', session('project_id') );
            $project_id = session('project_id');
        }

        $client_id = Project::where('id', $project_id)->first()->client_id;

        $user = User::create([
            'name'          => $customer['name'],
            'email'         => $customer['email'],
            'password'      => "qms1234",
            'contact'       => $customer['contact'],
            'current_role'  => '7',
        ]);

        $role_user = RoleUser::create([
            'user_id'       => $user->id,
            'role_id'       => '7',
            'project_id'    => $project_id,
            'client_id'     => $client_id,
        ]);

        $unit = DrawingPlan::where('name', $customer['unit'])->first();
        $unit->user_id = ($user->id);
        $unit->save();

        $user->notify(new CustomerEmail($project_id));
    }

    public function store_existing_customer($customer){
        $user = User::where('email',$customer['email'])->first();
        $user_id = $user->id;
        $project_id = session('project_id');
        if(Session::has('project_id')){
            Session::put('project_id', session('project_id') );
            $project_id = session('project_id');
        }

        $client_id = Project::where('id', $project_id)->first()->client_id;

        //store customer into new project
        $store_role = RoleUser::create([
            'user_id'       => $user_id,
            'role_id'       => '7',
            'project_id'    => $project_id,
            'client_id'     => $client_id, 
        ]);

        $unit = DrawingPlan::where('name', $customer['unit'])->first();
        $unit->user_id = ($user->id);
        $unit->save();

        //send email
        $user->notify(new ExistingCustomerEmail($project_id));
    }
}
