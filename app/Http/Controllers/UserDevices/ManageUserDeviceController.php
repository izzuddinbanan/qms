<?php

namespace App\Http\Controllers\UserDevices;

use App\Entity\UserDevice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ManageUserDeviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $param = request()->query();

        if (empty($param) || (!isset($param['sort']))) {
            $sort = "created_at";
            $order = "desc";
        } else {
            $sort = $param['sort'];
            $order = $param['order'];
        }

        $user_devices = UserDevice::
            orderBy($sort, $order)
            ->paginate(20);
        return view('user-devices.index', compact('user_devices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = \App\Entity\User::get();

        $os = collect();
        collect(UserDevice::$operating_systems)->each(function ($operating_system, $key) use ($os) {
            $item = (object) [];
            $item->id = ++$key;
            $item->name = $operating_system;
            $os->push($item);
        });

        return view('user-devices.create', compact('users', 'os'));
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
            'IMEI'        => 'required|max:255',
            'push_token'  => 'required|max:255',
            'OS'          => 'numeric',
            'app_version' => 'required|max:255',
            'os_version'  => 'required|max:255',
            'width'       => 'required|max:255',
            'height'      => 'required|max:255',
            'user_id'     => 'numeric',
        ];

        $message = [
            'user_id.numeric' => 'Please select one of the user.',
            'OS.numeric'      => 'Please select one of the OS.',
        ];

        $validator = \Validator::make($request->input(), $rules, $message);

        if ($validator->fails()) {

            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $input = $request->input();
        $input['OS'] = $input['OS'] == 1 ? 'ANDROID' : 'IOS';
        $user_device = UserDevice::create($input);

        return redirect()->route('user-devices.index')->with(['status' => 'New device successfully added', 'type' => 'success']);
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
        $user_device = UserDevice::find($id);
        $users = \App\Entity\User::get();

        $os = collect();
        collect(UserDevice::$operating_systems)->each(function ($operating_system, $key) use ($os) {
            $item = (object) [];
            $item->id = ++$key;
            $item->name = $operating_system;
            $os->push($item);
        });

        return view('user-devices.edit', compact('user_device', 'users', 'os'));
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
            'IMEI'        => 'required|max:255',
            'push_token'  => 'required|max:255',
            'OS'          => 'numeric',
            'app_version' => 'required|max:255',
            'os_version'  => 'required|max:255',
            'width'       => 'required|max:255',
            'height'      => 'required|max:255',
            'user_id'     => 'numeric',
        ];

        $message = [
            'user_id.numeric' => 'Please select one of the user.',
            'OS.numeric'      => 'Please select one of the OS.',
        ];

        $validator = \Validator::make($request->input(), $rules, $message);

        if ($validator->fails()) {

            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $input = $request->input();
        $input['OS'] = $input['OS'] == 1 ? 'ANDROID' : 'IOS';
        $user_device = UserDevice::find($id);
        $user_device->update($input);

        return redirect()->route('user-devices.index')->with(['status' => 'Device successfully updated', 'type' => 'success']);
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

            if (UserDevice::where('id', $id)->delete()) {
                return response()->json(['status' => 'ok']);
            }

        }
        return Response::json(['status' => 'fail']);
    }

    /**
     * @param Request $request
     */
    public function search(Request $request)
    {
        $param = request()->query();

        if (empty($param) || (!isset($param['sort']))) {
            $sort = "created_at";
            $order = "desc";
        } else {
            $sort = $param['sort'];
            $order = $param['order'];
        }

        $user_devices = UserDevice::search($request->input('filter'))
            ->with('user')
            ->orderBy($sort, $order)
            ->paginate(20);

        return view('user-devices.index', compact('user_devices'));
    }
}
