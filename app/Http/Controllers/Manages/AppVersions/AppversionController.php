<?php

namespace App\Http\Controllers\Manages\AppVersions;

use Lang;
use Validator;
use App\Entity\AppVersion;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;

class AppversionController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('app_version.index');
    }

    public function indexData () {

        $app = AppVersion::select(['app_versions.id', 'app_versions.os', 'app_versions.version', 'app_versions.type', 'app_versions.status', 'app_versions.created_at']);

        return Datatables::of($app)
        
            ->addColumn('action', function ($app) {
                
                $button = edit_button(route('app-version.edit', [$app->id]));
                $button .= delete_button(route('app-version.destroy', [$app->id]));
                
                return $button;
            })
            ->addColumn('status-label', function ($app) {
                
                return '<span class="label label-'. ($app->status == 'Active' ? 'success' : 'default')  .'">' . $app->status . '</label>';
            })
            ->editColumn('created_at', function ($app) {
                
                return $app->created_at->toDateTimeString();
            })
            ->rawColumns(['action', 'status-label'])
            ->make(true);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('app_version.create');
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
            'OS'        => 'required',
            'version'   => 'required',
            'type'      => 'required',
        ];

        $message = [];
        
        $this->validate($request, $rules, $message);


        $check_version = AppVersion::where('os', $request->input('OS'))
                        ->where('status', $request->input('status'))
                        ->whereNull('deleted_at')
                        ->orderBy('created_at', 'desc')
                        ->first();

        if($check_version){
            if(version_compare($request->input('version'), $check_version->version) <= 0) {
                $status = Lang::get('alert.lowerVersion');
                return redirect()->back()->withErrors($status)->withInput();
            }
        }

        if($request->input('status') == 'active')
        {
           AppVersion::where('os',$request->input('OS'))->where('type',$request->input('type'))->update(['status' => 'inactive']);
        }

        $Appversion = AppVersion::create([
            'os'             => $request->input('OS'),
            'version'        => $request->input('version'),
            'type'           => $request->input('type'),
            'description'    => $request->input('description'),
            'status'         => $request->input('status'),
            'created_at'     => \Carbon\Carbon::now('Asia/Kuala_Lumpur'),
            'updated_at'     => \Carbon\Carbon::now('Asia/Kuala_Lumpur'),
        ]);

        return redirect('app-version')->with(['success-message' => Lang::get('alert.successAdd') ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Appversion  $appversion
     * @return \Illuminate\Http\Response
     */
    public function show($appversion)
    {

        if(!$data = AppVersion::where("id", $appversion)->first()){
            return back();
        }
        return view('app_version.view', compact('data'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Appversion  $appversion
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if ($data = AppVersion::find($id)) {
            return view('app_version.edit', compact('data'));
        }

        return redirect()->route('app-version.index')->with(['warning-message' => 'Record not found.']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Appversion  $appversion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $appversion)
    {

        $rules = [
            'OS'        => 'required',
            'version'   => 'required',
            'type'      => 'required',
        ];
        $message = [];
        $this->validate($request, $rules, $message);

        $check_version = AppVersion::where('os', $request->input('OS'))
                        ->where('status', $request->input('status'))
                        ->whereNull('deleted_at')
                        ->orderBy('created_at', 'desc')
                        ->first();

        if($check_version){
            if(version_compare($request->input('version'), $check_version->version) < 0) {
                $status = Lang::get('alert.lowerVersion');
                return redirect()->back()->withErrors($status)->withInput();
            }
        }

        if($request->input('status') == 'active')
        {
           AppVersion::where('os',$request->input('OS'))->where('type',$request->input('type'))->update(['status' => 'inactive']);
        }

        $appversion = AppVersion::find($appversion);
        
        $appversion->update([
            'os'             => $request->input('OS'),
            'version'        => $request->input('version'),
            'type'           => $request->input('type'),
            'description'    => $request->input('description'),
            'status'         => $request->input('status'),
            'updated_at'     => \Carbon\Carbon::now('Asia/Kuala_Lumpur'),
        ]);

        $appversion ->save();

        return redirect('app-version')->with(['success-message' => Lang::get('alert.successUpdate'), 'type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AppVersion  $appversion
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        if ($request->ajax()) {

            if ($app = AppVersion::find($id)) {

                $app->delete();
                
                return response()->json(['status' => 'ok']);
            }

        }
        return Response::json(['status' => 'fail']);

    }
  
}
