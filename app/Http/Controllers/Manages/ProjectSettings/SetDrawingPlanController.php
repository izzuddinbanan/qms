<?php

namespace App\Http\Controllers\Manages\ProjectSettings;

use App\Entity\DrawingPlan;
use App\Entity\DrawingSet;
use App\Entity\LocationPoint;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Session, Storage, File, Response;

class SetDrawingPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect()->route('set-drawing-set.index', session('project_id'));
    }

    public function indexData ()
    {
        $drawing_plan = DrawingPlan::where('drawing_set_id', session('drawing_set_id'))->select(['drawing_plans.*']);

        return Datatables::of($drawing_plan)
            ->addColumn('image', function ($drawing_plan) {
                    
                return $image = '<a href="'. $drawing_plan->file_url .'" data-popup="lightbox">
                            <img src="'. $drawing_plan->file_url .'" class="img-responsive" style="height: 50px;width: 50px;">
                            </a>';
            })
            ->editColumn('name', function ($drawing_plan) {
                
                if($drawing_plan->default){
                    return '<span class="label label-success">'. $drawing_plan->name .'</span>';
                }

                return $drawing_plan->name;
            })
            ->editColumn('phase', function ($drawing_plan) {
                
                return $drawing_plan->phase ?? '-';
            })
            ->editColumn('block', function ($drawing_plan) {
                
                return $drawing_plan->block ?? '-';
            })
            ->editColumn('level', function ($drawing_plan) {
                
                return $drawing_plan->level ?? '-';
            })
            ->editColumn('unit', function ($drawing_plan) {
                
                return $drawing_plan->unit ?? '-';
            })
            ->addColumn('action', function ($drawing_plan) {
                
                $button = '<a href="#" data-popup="tooltip" title="'. trans('main.edit') .'" data-placement="top" onclick="return editForm(' . $drawing_plan->id .')" class="tooltip-show"><i class="fa fa-pencil-square-o fa-lg"></i></a>';
               
                $button .= ' <a href="#" data-popup="tooltip" title="Clone" data-placement="top" onclick="return clonePlan(' . $drawing_plan->id .')" class="tooltip-show"><i class="fa fa-copy fa-lg"></i></a>';

                if(!$drawing_plan->default){
                    $button .= ' <a href="'. route('set-drawing-plan.default', [$drawing_plan->id]) .'" data-popup="tooltip" title="Set as Default" data-placement="top" class="tooltip-show"><i class="fa fa-check-square-o fa-lg"></i></a> ';
                    $button .= delete_button(route('set-drawing-plan.destroy', [$drawing_plan->id]));

                }
                
                return $button;
            })
            ->rawColumns(['name', 'image', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!session('drawing_set_id')) {
            return $this->index();
        }

        if(!$drawing = DrawingSet::where('id', session('drawing_set_id'))->where('project_id', session('project_id'))->first()){
            return back()->withErrors('Drawing set not found.');
        }

        return view('project-settings.set-drawing-plans.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if(!DrawingSet::find(session('drawing_set_id'))){

            return back()->with(['warning-message' => 'Record not found.']);
        }
        $rules = [
            'mode'       => 'required',
        ];

        $message = [
            'mode.required' => 'Please select mode first.',
        ];

        $this->validate($request, $rules, $message);


        ## ADDITIONAL VALIDATION BASE ON MODE
        if($request->input('mode') == 'single') {

            if($request->input('type_plan') == 'unit') {

                $rules = [
                    'unit'               => 'required',
                ];
            }

           $rules = array_merge($rules, ['drawing_plan' => 'required|image|mimes:jpg,png,jpeg', 'display_name'  => 'required']);
        }

        ## ADDITIONAL VALIDATION BASE ON MODE
        if($request->input('mode') == 'batch') {
            $rules = [
                'batch_file'       => 'required',
            ];

            $message = [];
            $this->validate($request, $rules, $message);

            if($request->file('batch_file')->getClientOriginalExtension() != 'zip'){
                return back()->withErrors("File upload must be in zip format")->withInput();   
            }
        }

        $message = [];
        $this->validate($request, $rules, $message);

        $message = [];
        $this->validate($request, $rules, $message);
        ## VALIDATION 

        switch ($request->input('mode')) {
            case 'single':
                
                $this->singleMode($request);

                break;
            case 'batch':
                
                return $this->batchMode($request);

                break;
        }


        return redirect()->route('set-drawing-plan.show', [session('drawing_set_id')])->with(['success-message' => 'New record successfully added.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        if(!$drawing = DrawingSet::where('id', $id)->where('project_id', session('project_id'))->first()){
            return back()->withErrors('Drawing set not found.');
        }

        $listDrawSets = DrawingSet::where('project_id', session('project_id'))->get();

        Session::forget('drawing_set_id');
        Session::put('drawing_set_id', $drawing->id);
        $allPlan = DrawingPlan::where('drawing_set_id', $drawing->id)->get();

        return view('project-settings.set-drawing-plans.index', compact('allPlan', 'listDrawSets', 'drawing'));

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
        if(!$plan = DrawingPlan::where('drawing_set_id', session('drawing_set_id'))->where('id', $request->input('plan_id'))->first()){
            return back()->with(['warning-message' => "Error. Record not found."]);
        }

        $plan->update([
            'types'                 => $request->input('type_plan'),
            'name'                  => $request->input('plan_name'),
            'phase'                 => $request->input('type_plan') != 'custom' ? $request->input('phase') : null,
            'block'                 => $request->input('type_plan') != 'custom' ? $request->input('block') : null,
            'level'                 => $request->input('type_plan') != 'custom' ? $request->input('level') : null,
            'unit'                  => $request->input('type_plan') != 'custom' ? $request->input('unit') : null,
        ]);

        if($plan->seq != $request->input('plan_seq')) {

            $maxSeq = DrawingPlan::where('drawing_set_id', session('drawing_set_id'))->orderby('seq', 'DESC')->first();

            $new_seq = $request->input('plan_seq') <= $maxSeq->seq ? $request->input('plan_seq') : $maxSeq->seq;


            if($plan->seq > $request->input('plan_seq')) {

                $drawPlan = DrawingPlan::where('drawing_set_id', session('drawing_set_id'))->where('seq', '>=', $new_seq)->where('seq', '<', $plan->seq)->get();

                foreach ($drawPlan as $key => $value) {
                    DrawingPlan::where('id', $value->id)->update(['seq' => ++$value->seq]);
                }

            }else {

                $drawPlan = DrawingPlan::where('drawing_set_id', session('drawing_set_id'))->where('seq', '<=', $new_seq)->where('seq', '>', $plan->seq)->get();
                foreach ($drawPlan as $key => $value) {
                    DrawingPlan::where('id', $value->id)->update(['seq' => --$value->seq]);
                }

            }

            $plan->update([
                'seq'      => $new_seq,
            ]);

        }

        return back()->with(['success-message' => "Record successfully updated."]);

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


            if ($DrawingPlan = DrawingPlan::find($id)) {

                if(!$DrawingSet = DrawingSet::where('project_id', session('project_id'))->where('id', session('drawing_set_id'))->first())
                {
                    return Response::json(['status' => 'fail']);
                }

                ##default image cant be deleted
                if($DrawingPlan->default == 1){
                    return Response::json(['status' => 'fail']);
                }
                
                File::delete(public_path('uploads/drawings/'.  $DrawingPlan->file));
                File::delete(public_path('uploads/drawings/thumbnail/'.  $DrawingPlan->file));
                $DrawingPlan->delete();

                $plan = DrawingPlan::where('drawing_set_id', session('drawing_set_id'))->orderBy('seq')->get();

                $i = 1;
                foreach ($plan as $key => $value) {
                    DrawingPlan::where('id', $value->id)->update(['seq' => $i++]);
                }

                return response()->json(['status' => 'ok']);
            }

        }
        return Response::json(['status' => 'fail']);
        
    }


    public function singleMode($request){

        $lastSeq = 0;
        $set_id = session('drawing_set_id');

        if($drawingSeq = DrawingPlan::where('drawing_set_id', $set_id)->orderby('seq', 'DESC')->first()){
            $lastSeq = $drawingSeq->seq;
        }

        $image = \App\Processors\SaveDrawingPlanProcessor::make($request->file('drawing_plan'))->execute();


        // $unit = $request->input('block') . '-' . $request->input('level') . '-' . $request->input('unit'); 
        // $name = $request->input('type_plan') != 'unit' ? $request->input('display_name') : $unit;

        $drawing_plan = DrawingPlan::create([
            'drawing_set_id'        => $set_id,
            'file'                  => $image["name_unique"], 
            'width'                 => $image["width"],
            'height'                => $image["height"],
            'seq'                   => ++$lastSeq,
            'types'                 => $request->input('type_plan'),
            'name'                  => $request->input('display_name'),
            'phase'                 => $request->input('type_plan') != 'custom' ? $request->input('phase') : null,
            'block'                 => $request->input('type_plan') != 'custom' ? $request->input('block') : null,
            'level'                 => $request->input('type_plan') != 'custom' ? $request->input('level') : null,
            'unit'                  => $request->input('type_plan') != 'custom' ? $request->input('unit') : null,
        ]);  

        // if ($request->input('type_plan') != 'custom') { 
        //     $this->setupInitialZone($drawing_plan);
        // }   


        $drawingSet = DrawingSet::where('project_id', session('project_id'))->select('id')->get();

        if(!$findDefault = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->where('default', 1)->first()){
            DrawingPlan::where('seq', 1)->whereIn('drawing_set_id', $drawingSet)->update(['default' => 1]);
        }


    }

    public function batchMode($request)
    {

        $file = $request->file('batch_file');
        
        $temp_file = rand(99,10000) . time().'.zip';

        $batch_folder = 'batch_upload/';

        $destinationPath = public_path('uploads/' . $batch_folder);
        $file->move($destinationPath, $temp_file);
      
        $path = $destinationPath. '/' . $temp_file;
       
        // create temp folder
        $common = $destinationPath . '/' . 'common_'.date("ymdhisa");
        $unit = $destinationPath . '/' . 'unit_'.date("ymdhisa");
        $custom = $destinationPath . '/' . 'custom_'.date("ymdhisa");

        $common_folder = 'common_'.date("ymdhisa");
        $unit_folder = 'unit_'.date("ymdhisa");
        $custom_folder = 'custom_'.date("ymdhisa");

        // extract into folder
        \Zipper::make($path)->folder('common')->extractTo($common);
        \Zipper::make($path)->folder('unit')->extractTo($unit);
        \Zipper::make($path)->folder('custom')->extractTo($custom);
        
        // Get list all file in extracted zip folder
        $filesInCommon = \File::files($common);
        $filesInUnit = \File::files($unit);
        $filesInCustom = \File::files($custom);
        $acceptFormat = array('png', 'jpg', 'jpeg');
        $type = array('common' => $filesInCommon, 'unit' => $filesInUnit, 'custom' => $filesInCustom);

        $lastSeq = 0;
        $total_success = 0;
        $plan = array();

        if($drawingSeq = DrawingPlan::where('drawing_set_id', session('drawing_set_id'))->orderby('seq', 'DESC')->first()){
            $lastSeq = $drawingSeq->seq;
        }

        foreach ($type as $planTye => $filesInFolder) {

            foreach ($filesInFolder as $key => $path)
            {   


                if(in_array(strtolower(pathinfo($path)["extension"]), $acceptFormat)){
                    
                    do{
                        $name_unique = time() . rand(10, 99) . '.png';
                    }while(($name_unique == Storage::exists('uploads/drawings/'. $name_unique)));
                    
                    if($planTye != 'custom') {

                        $name = explode('_', pathinfo($path)["filename"]);
                        
                        $plan_block = $name[1] . '-';
                        $plan_level = $name[2] . '-';
                        $plan_unit = $name[3];

                        $plan_name = $plan_block .'' . $plan_level .'' . $plan_unit;


                        if(count($name) == 4){

                            ++$lastSeq;
                            $total_success++;

                            $plan["success"][$planTye][] = pathinfo($path);
                            $commonUnitFile = pathinfo($path);
                            
                            if($planTye == 'common'){
                                $file = $common;
                                $folder = $common_folder;
                            }else{
                                $file = $unit;
                                $folder = $unit_folder;
                            }
                            
                            $imageDimension = getImageSize($file . '/' . $commonUnitFile['basename']);
                            
                            Storage::move(('/uploads/' . $batch_folder . $folder) . '/' . $commonUnitFile['basename'], 'uploads/drawings/' . $name_unique );

                            $drawing_plan = DrawingPlan::create([
                                'drawing_set_id'        => session('drawing_set_id'),
                                'name'                  => $plan_name,
                                'file'                  => $name_unique, 
                                'width'                 => $imageDimension[0],
                                'height'                => $imageDimension[1],
                                'seq'                   => $lastSeq,
                                'types'                 => $planTye,
                                'phase'                 => $name[0],
                                'block'                 => $name[1],
                                'level'                 => $name[2],
                                'unit'                  => $name[3],
                            ]);

                        }else{
                            $plan["fail"][$planTye][] = pathinfo($path);
                            $plan["fail"][$planTye][$key]["message"] = "Name of image not follow the format";
                        }
                    }else{
                        // CUSTOM PLAN UPLOAD
                        ++$lastSeq;
                        $total_success++;

                        $plan["success"][$planTye][] = pathinfo($path);
                        $customFile = pathinfo($path);
                        
                        $imageDimension = getImageSize(($custom . '/' . $customFile['basename']));
                        
                        Storage::move(('/uploads/' . $batch_folder . $custom_folder)  . '/' . $customFile['basename'], 'uploads/drawings/' . $name_unique );

                        DrawingPlan::create([
                            'drawing_set_id'        => session('drawing_set_id'),
                            'name'                  => $customFile["filename"],
                            'file'                  => $name_unique, 
                            'width'                 => $imageDimension[0],
                            'height'                => $imageDimension[1],
                            'seq'                   => $lastSeq,
                            'types'                 => 'custom',
                            'block'                 => null,
                            'level'                 => null,
                            'unit'                  => null,
                            'phase'                 => null,
                        ]);

                    }

                }else{
                    $plan["fail"][$planTye][] = pathinfo($path);
                    $plan["fail"][$planTye][$key]["message"] = "Image format not supported.";
                }

            }

        }

        $drawingSet = DrawingSet::where('project_id', session('project_id'))->select('id')->get();
        if(!$findDefault = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->where('default', 1)->first()){
            DrawingPlan::where('seq', 1)->whereIn('drawing_set_id', $drawingSet)->update(['default' => 1]);
        }

        File::deleteDirectory($common);
        File::deleteDirectory($unit);
        File::deleteDirectory($custom);
        File::deleteDirectory(public_path('uploads/' . $batch_folder));

        $total_files = sizeof($filesInCustom) + sizeof($filesInUnit) + sizeof($filesInCommon);

        return back()->with(['total_success' => $total_success, 'total_files' => $total_files, 'data' => $plan ,'success_upload' => "modal_report_batch_upload"]);

    }


    public function setupInitialZone($drawing_plan) {

        ## REMOVE THIS FUNCTION ## NEED TO CHECK WHETER NEED OR NOT 
        ## FUNCTION TO MAKE OTHER LOCATION

        // $location = LocationPoint::create([
        //     'name'              =>  "Other",
        //     'drawing_plan_id'   =>  $drawing_plan->id,
        //     'status_id'         =>  1,
        //     'points'            =>  implode(',', [0,0,0,$drawing_plan->height, $drawing_plan->width, $drawing_plan->height, $drawing_plan->width, 0]),
        //     'color'             =>  "#000000",
        // ]);

        // $now = \Carbon\Carbon::now('Asia/Kuala_Lumpur');
        // $format = date("dmy", strtotime($now));
        // $unique_ref = $format . '-P' . $location->drawing_plan_id . 'L' . $location->id . '-R0'; 

        // $location->forcefill(['reference' => $unique_ref])->save();
    }

    public function setDefault($drawing_id)
    {
        if(!$plan = DrawingPlan::where('drawing_set_id', session('drawing_set_id'))->where('id', $drawing_id)->first()){
            return back()->with(['warning-message' => "Record not found."]);
        }

        $drawing_set = DrawingSet::where('project_id', session('project_id'))->select('id')->get();
        DrawingPlan::whereIn('drawing_set_id', $drawing_set)->where('default', true)->update(['default' => false]);
        
        $plan->update([
            'default' => 1
        ]);

        return back()->with(['success-message' => "Record successfully updated."]);

    }

    public function clonePlan(Request $request)
    {   

        $rules = [
            'plan_id_clone'          => 'required',
            'plan_clone'             => 'required|numeric|min:1',
        ];

        $message = [
            'plan_id_clone.required'    => 'Please select drawing plan',
            'plan_clone.required'       => 'please input your number of copies',
        ];

        $this->validate($request, $rules, $message);
        
        if(!$plan = DrawingPlan::where('drawing_set_id', session('drawing_set_id'))->where('id', $request->input('plan_id_clone'))->first()){
            return back()->with(['warning-message' => "Record not found."]);
        }


        $set_id = session('drawing_set_id');

        $number = $request->input('plan_clone');

        $lastSeq = 0;
        if($drawingSeq = DrawingPlan::where('drawing_set_id', $set_id)->orderby('seq', 'DESC')->first()){
            $lastSeq = $drawingSeq->seq;
        }


        for($i = 1; $i <= $number; $i++){
            ++$lastSeq;
            $newPlan = $plan->replicate();

            $newPlan->save();
            $newPlan->drawing_set_id = $set_id;
            $newPlan->default = 0;


            $current_copy = DrawingPlan::where('file', $newPlan->file)->count();

            $newPlan->name = $newPlan->name . '('. $current_copy .')';
            $newPlan->seq = $lastSeq;
            $newPlan->save();

        }

        return back()->with(['success-message' => "New Record successfully created."]);


    }

}
