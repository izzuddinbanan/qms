<?php

namespace App\Http\Controllers\Manages\Clients;

use File, Validator, Lang;
use App\Entity\User;
use App\Entity\RoleUser;
use App\Entity\Client;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;



use Yajra\Datatables\Datatables;


class ClientController extends Controller
{

    public function __construct(Request $request)
    {
        $this->middleware('isSuperUser');
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        return view('client.index');
    }


    public function indexData()
    {
        $client = Client::select(['id', 'name', 'abbreviation_name', 'logo', 'created_at']);
        
        return Datatables::of($client)
        	->addColumn('image_logo', function ($client) {
                	
                return $image = '<a href="'. $client->logo_url .'" data-popup="lightbox">
                            <img src="'. $client->logo_url .'" class="img-responsive" style="height: 50px;width: 50px;">
                            </a>';
            })
            ->addColumn('action', function ($client) {

        		$role_user = RoleUser::where('client_id', $client->id)->where('role_id', get_role('power_user')->id)->first();

			    $button = '<a href="'. route('switchUser', [$role_user->user_id]) .'" id="switch_'. $role_user->user_id .'" onclick="switchALert('. $role_user->user_id .', event)" data-popup="tooltip" title="switch user" data-placement="top" class="tooltip-show"><i class="fa fa-exchange action-icon"></i></a>';
                $button .= edit_button(route('client.edit', [$client->id]));
                $button .= delete_button(route('client.destroy', [$client->id]));
                
                return $button;
            })
            ->editColumn('created_at', function ($app) {
                
                return $app->created_at->format('d/m/Y h:i a');
            })
            ->rawColumns(['action', 'image_logo'])
            ->make(true);
    }


	public function create()
	{
		return view('client.create');
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
			'client_name'       => 'required|max:255',
			'abbreviation_name' => 'required|max:20',
			'user_name'         => 'required|max:255',
			'password'          => 'required|max:255',
			'contact'           => 'numeric|nullable',
			'email'             => 'required|email|unique:users',
			'logo'             	=> 'image|mimes:jpg,png,jpeg|',
			'app_logo'          => 'image|mimes:jpg,png,jpeg|',
	    ];

        $message = [];
        
        $this->validate($request, $rules, $message);


	    $client = Client::create([
			'name'              => $request->input('client_name'),
			'abbreviation_name' => $request->input('abbreviation_name'),
			'logo'			  => $request->hasFile('logo') ? \App\Processors\SaveLogoClientProcesscor::make($request->file('logo'))->execute() : null,
			'app_logo'			  => $request->hasFile('app_logo') ? \App\Processors\SaveLogoClientProcesscor::make($request->file('app_logo'))->execute() : null,
	    ]);

	    $user = User::create([
			'name'        => $request->input('user_name'),
			'email'       => $request->input('email'),
			'verified'    => 1,
			'password'    => bcrypt($request->input('password')),
			'contact'     => $request->input('contact'),
	    ]);

	    $roleuser = RoleUser::create([
			'user_id'      => $user->id,
			'role_id'      => get_role('power_user')->id,
			'client_id'    => $client->id,
	    ]);


	    return redirect()->route('client.index')->with('success-message', Lang::get('alert.successAdd') );
      
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
    
		if(!$data = Client::find($id)){
		return back()->withErrors(Lang::get('alert.notFound'));
		}

		$data = Client::PowerUser()->where('clients.id', $id)->first();

		return view('client.edit', compact('data'));
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
		$rules = [
			'client_name'       => 'required|max:255',
			'abbreviation_name' => 'required|max:20',
			'user_name'         => 'required|max:255',
			'contact'           => 'numeric|nullable',
		];

    	$message = [];

        $this->validate($request, $rules, $message);

		if(!$client = Client::find($id)){
			return back()->withErrors(Lang::get('alert.notFound'))->withInput();
		}

		$client->update([
			'name'              => $request->input('client_name'),
			'abbreviation_name' => $request->input('abbreviation_name')
		]);

		$user = User::where('id', $request->input('user_id'))
				->update([
                    'name'    => $request->input('user_name'),
                    'contact'    => $request->input('contact')
            	]);

        if ($request->hasFile('logo')) {

        	if($client->logo){
				File::delete(public_path('uploads/client_logo/') .  $client->logo);
        	}

        	$client->logo = \App\Processors\SaveLogoClientProcesscor::make($request->file('logo'))->execute();
		}

		if ($request->hasFile('app_logo')) {

        	if($client->app_logo){
				File::delete(public_path('uploads/client_logo/') .  $client->app_logo);
        	}
        	
        	$client->app_logo = \App\Processors\SaveLogoClientProcesscor::make($request->file('app_logo'))->execute();
		}

		$client->save();

    	return redirect()->route('client.index')->with(['success-message' => Lang::get('alert.successUpdate')]);

  }


    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {

            if ($client = Client::find($id)) {

                
                if ($client->logo) {
					File::delete(public_path('uploads/client_logo/') .  $client->logo);
				}
				if ($client->app_logo) {
				  File::delete(public_path('uploads/client_logo/') .  $client->app_logo);
				}


				$role_user = RoleUser::where('client_id', $client->id)->get();

				foreach ($role_user as $key => $value) {

					if(!RoleUser::where('user_id', $value->user_id)->whereNotIn('client_id', [$client->id, 0, null])->first()) {

						$user = User::find($value->user_id);

						$user->email = $user->email . '|deleted-at :' . NOW();
						$user->save();
						$user->delete();

					}
				}

				RoleUser::where('client_id', $client->id)->delete();
				$client->delete();

				## NEED TO CHECK PROJECT | EVERYTHIIG DELETED | LATER

                return response()->json(['status' => 'ok']);
            }

        }
        return Response::json(['status' => 'fail']);
    }

}
