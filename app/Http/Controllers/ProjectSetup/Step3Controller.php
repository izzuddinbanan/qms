<?php

namespace App\Http\Controllers\ProjectSetup;

use Validator;
use App\Entity\DrawingSet;
use App\Entity\DrawingPlan;
use App\Entity\RoleUser;
use App\Entity\DrillDown;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Step3Controller extends Controller
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

        return view('project.step3', compact('id', 'data', 'default'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
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
            'set'           => 'required',
            'link_to_plan'  => 'required',
        ];

        $message = ['set.required'   => 'Drawing set is required.',
                    'link_to_plan.required'     => 'Drawing plan is required.'];

        $validator = Validator::make($request->input(), $rules, $message); 

        $DrillDown = DrillDown::create([
            'drawing_plan_id'       => $request->input('drawing_plan_from'),
            'to_drawing_plan_id'    => $request->input('link_to_plan'),
            'position_x'            => $request->input('position_x'),
            'position_y'            => $request->input('position_y'),
        ]);

        $drawing_plan = DrawingPlan::find($request->input('link_to_plan'));

        $data = array('drill_id' => $DrillDown->id , 'pos_x' => $DrillDown->position_x , 'pos_y' => $DrillDown->position_y , 'link_id' => $drawing_plan->id, 'link_name' => $drawing_plan->name );
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
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $DrillDown = DrillDown::find($request->input('point_id'));

        $DrillDown->to_drawing_plan_id = $request->input('update_link_to_plan');

        if (isset ($request->all()['x'])) {
            $DrillDown->position_x = $request->input('x');
        }

        if (isset ($request->all()['y'])) {
            $DrillDown->position_y = $request->input('y');
        }

        $DrillDown->save();

        // $DrillDown = DrillDown::find($request->input('point_id'));
        $drawing_plan = DrawingPlan::find($request->input('update_link_to_plan'));

        $data = array('drill_id' => $DrillDown->id , 'pos_x' => $DrillDown->position_x , 'pos_y' => $DrillDown->position_y , 'link_id' => $drawing_plan->id, 'link_name' => $drawing_plan->name );

        return $data;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {

        if($data = DrillDown::where('id', $request->id)->where('drawing_plan_id', $request->drawing_plan)->first()){
            // $data = LocationPoint::find($request->id);
            $data->delete();

            $msg = array('type' => 'success', 'msg' => "Link already removed",);
            return $msg;
        }else{
            $msg = array('type' => 'error', 'msg' => "Link not found",);

            return $msg;
        }
    }


    public function listPlan(Request $request){

        if($request->input('drill_id')){

            $drill = DrillDown::find($request->input('drill_id'));

            $data = DrawingPlan::where('drawing_set_id', $request->input('drawing_set'))->where('id', "!=", $request->input('drawing_plan'))->get();


            return json_encode([$drill, $data]);

        }


        if($request->input('drawing_plan')){
            $data = DrawingPlan::where('drawing_set_id', $request->input('drawing_set'))->where('id', "!=", $request->input('drawing_plan'))->get();

            return $data;
        }

        $data = DrawingPlan::where('drawing_set_id', $request->input('drawing_set'))->orderBy('seq')->get();

        return $data;
    }

    public function viewPlan(Request $request){

        $plan = DrawingPlan::with('drill')->find($request->input('drawing_plan'));

        $drill = DrillDown::with('link')->where('drawing_plan_id', $plan->id)->get();

        $data = array("drawing_plan" => $plan, "drill" => $drill);
        return $data;
    }

    public function getPos(Request $request){

        $data = DrillDown::find($request->input('point_id'));

        return $data;
    }

    function updatePosition(Request $request){

        $drill = DrillDown::find($request->input('id'));

        $drill->update([

            'position_x' => $request->input('x'),
            'position_y' => $request->input('y'),

        ]);

        return $drill;
    }

    function detailsMarker(Request $request){

        $drill = DrillDown::find($request->input('id'));

        $drawingSet = DrawingPlan::find($drill->to_drawing_plan_id);

        return ['drill' => $drill, 'set' => $drawingSet];

    }


    function getAllSet(){

        $data = DrawingSet::where('project_id', session('project_id'))
            ->with(['drawingPlan' => function($query) {
                $query->orderBy('seq');}])
            ->get();

        return $data;

    }

}
