<?php

namespace App\Http\Controllers\ProjectSetup;

use Validator;
use App\Entity\Language;
use App\Entity\Project;
use App\Entity\DrawingSet;
use App\Entity\DrawingPlan;
use App\Entity\LocationPoint;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Step4Controller extends Controller
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
        $id = session('project_id');
        
        $data = DrawingSet::where('project_id', $id)->orderBy('seq')->get();
        $drawingSet = DrawingSet::where('project_id', $id)->select('id')->get();
        $default = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->where('default', 1)->first();


        $project = Project::find($id);

        $language = Language::get();

        $langSetup = explode(',', $project->language_id);

        return view('project.step4', compact('id', 'data', 'default', 'language', 'langSetup'));
        
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
            'name'           => 'required',
        ];

        $message = [];

        $validator = Validator::make($request->input(), $rules, $message); 
        
        $location = LocationPoint::create([
            'name'              =>  $request->input('name'),
            'drawing_plan_id'   =>  $request->input('drawing_plan_id'),
            'status_id'         =>  1,
            'points'            =>  $request->input('points'),
            'color'             =>  $request->input('color'),
        ]);


        // $location->normalGroupForm()->detach();
        // $location->normalForm()->detach();
        // $location->mainForm()->detach();
        // $location->mainGroupForm()->detach();

        $normalForm = array(); 
        $groupNormalForm = array(); 
        if($request->input('normal_form')){

            foreach ($request->input('normal_form') as $value) {

                $type = substr($value, 0,1);

                if($type == 's'){
                    // $location->normalForm()->attach(substr($value, 2));

                    $normalForm[] = substr($value, 2);
                }else{

                    $groupNormalForm[] = substr($value, 2);

                    // $location->normalGroupForm()->attach(substr($value, 2));

                }
            }
        }


        $groupHandForm = array();
        $handForm = array();

        if($request->input('hand_form')){

            foreach ($request->input('hand_form') as $value) {

                $type = substr($value, 0,1);

                if($type == 's'){
                    $handForm[] = substr($value, 2);
                    // $location->mainForm()->attach(substr($value, 2));
                }else{
                    $groupHandForm[] = substr($value, 2);
                    // $location->mainGroupForm()->attach(substr($value, 2));
                    
                }
            }
        }

        $location->forcefill(['normal_form' => empty(implode(',', $normalForm)) ? null : implode(',', $normalForm),
                                'normal_group_form' =>  empty(implode(',', $groupNormalForm)) ? null : implode(',', $groupNormalForm),
                                'main_form' => empty(implode(',', $handForm)) ? null : implode(',', $handForm),
                                'main_group_form' => empty(implode(',', $groupHandForm)) ? null : implode(',', $groupHandForm),
                            ]);

        $last = LocationPoint::where('drawing_plan_id', $request->input('drawing_plan_id'))->withTrashed()->count(); 

        $now = \Carbon\Carbon::now('Asia/Kuala_Lumpur');
        $format = date("dmy", strtotime($now));

        $unique_ref = $format . '-P' . $location->drawing_plan_id . 'L' . $location->id . '-R' . $last; 

        $location->forcefill(['reference' => $unique_ref])->save();

        $data = array('id' => $location->id , 'points' => $location->points , 'name' => $location->name, 'color' => $location->color, 'reference' => $location->reference );
        return $data;
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
        $location = LocationPoint::where('id', $id)
            ->where('drawing_plan_id',  $request->input('drawing_plan_id'))->first();

        $location->update([
            'name'              =>  $request->input('name'),
            'points'            =>  $request->input('points'),
            'color'             =>  $request->input('color'),
        ]);

        $normalForm = array(); 
        $groupNormalForm = array(); 
        if($request->input('normal_form')){

            foreach ($request->input('normal_form') as $value) {

                $type = substr($value, 0,1);

                if($type == 's'){
                    // $location->normalForm()->attach(substr($value, 2));

                    $normalForm[] = substr($value, 2);
                }else{

                    $groupNormalForm[] = substr($value, 2);

                    // $location->normalGroupForm()->attach(substr($value, 2));

                }
            }
        }


        $groupHandForm = array();
        $handForm = array();

        if($request->input('hand_form')){

            foreach ($request->input('hand_form') as $value) {

                $type = substr($value, 0,1);

                if($type == 's'){
                    $handForm[] = substr($value, 2);
                    // $location->mainForm()->attach(substr($value, 2));
                }else{
                    $groupHandForm[] = substr($value, 2);
                    // $location->mainGroupForm()->attach(substr($value, 2));
                    
                }
            }
        }

        $location->forcefill(['normal_form' => empty(implode(',', $normalForm)) ? null : implode(',', $normalForm),
                'normal_group_form' =>  empty(implode(',', $groupNormalForm)) ? null : implode(',', $groupNormalForm),
                'main_form' => empty(implode(',', $handForm)) ? null : implode(',', $handForm),
                'main_group_form' => empty(implode(',', $groupHandForm)) ? null : implode(',', $groupHandForm),
            ]);
        $location->save();

        return $location;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if($data = LocationPoint::where('id', $request->input('id'))->where('drawing_plan_id', $request->input('drawing_plan'))->with('issues')->first()){
            
            $data->delete();
            $msg = array('type' => 'success', 'msg' => "Location already removed",);

            return $msg;
        }else{
            $msg = array('type' => 'error', 'msg' => "Location not found",);

            return $msg;
        }
    }

    function updatePosition(Request $request){

        $location = LocationPoint::find($request->input('id'));

        $location->update([

            'position_x' => $request->input('x'),
            'position_y' => $request->input('y'),

        ]);

        return $location;
    }


    public function viewPlan(Request $request){

        $data = DrawingPlan::with('location')->find($request->input('drawing_plan'));

        return $data;
    }

    public function detailsMarker(Request $request){

        $data = LocationPoint::find($request->input('id'));

        return $data;
    }

}
