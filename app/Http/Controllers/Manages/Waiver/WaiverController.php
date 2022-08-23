<?php

namespace App\Http\Controllers\Manages\Waiver;

use App\Entity\HandOverMenu;
use App\Entity\HandoverFormWaiver;
use App\Http\Controllers\Controller;
use File, Session;
use Illuminate\Http\Request;

class WaiverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $project_id = session('project_id');
        $handover_menu = HandOverMenu::where('project_id', $project_id)->where('original_name', 'waiver')->first();
        $waiver = HandoverFormWaiver::where('project_id', $project_id)->first();
        return view('waiver.index', compact('handover_menu', 'waiver'));
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
            'content'             => 'required',
        ];

        $message = [];
        
        $this->validate($request, $rules, $message);

        if(!$request->waiver_id){
            HandoverFormWaiver::create([
                'project_id'    => session('project_id'),
                'description'   => $request->input('content'),
            ]);

        }else{

            HandoverFormWaiver::where('id', $request->waiver_id)->where('project_id', session('project_id'))->update([
                'description'   => $request->input('content'),
            ]);

        }

        return redirect()->route('waiver.index')->with(['success-message' => 'Waiver form successfully updated.']);
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
        $handover_menu = HandOverMenu::where('project_id', session('project_id'))->where('original_name', 'waiver')->first();

        if($id == 0 ){
            return view('waiver.edit', compact('handover_menu'));
        }else{

            $waiver = HandoverFormWaiver::find($id);
            return view('waiver.edit', compact('handover_menu', 'waiver'));
        }
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
