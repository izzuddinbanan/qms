<?php

namespace App\Http\Controllers\ProjectSetup;

use File;
use Auth;
use Validator;
use Storage;
use Session;
use App\Entity\Project;
use App\Entity\DrawingSet;
use App\Entity\Language;
use App\Entity\DrawingPlan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Input;
use App\Entity\LocationPoint;


class Step2Controller extends Controller
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

        $project = Project::find($id);
        $language = Language::get();

        $langSetup = explode(',', $project->language_id);


        $data = DrawingSet::where('project_id', $id)
                            ->with('drawingPlan')
                            ->orderBy('seq')->get();
        return view('project.step2', compact('id', 'data', 'langSetup', 'language'));
        
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
        $id = session('project_id');
        
        if(!$project = Project::find($id)){
            return back()
                ->withErrors('Project not found.')
                ->withInput();
        }

        $rules = [
            'drawing_name'  => 'required',
        ];

        $validator = Validator::make($request->input(), $rules);  

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->with(['modal' => 'show'])
                ->withInput();
        }

        $lastSeq = 0;
        if($drawingSeq = DrawingSet::where('project_id', $id)->orderby('seq', 'DESC')->first()){
            $lastSeq = $drawingSeq->seq;
        }

        $DrawingSet = DrawingSet::create([
            'project_id'        => $id,
            'seq'               => ++$lastSeq,
            'name'              => $request->input('drawing_name'),
            'handover_key_id'   => $request->input('key_form'),
            'handover_es_id'    => $request->input('es_form'),
            'handover_form'     => $request->input('close_and_handover_form'),
        ]);


        return back()->with(['success-message' => 'New record successfully added.']);


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($set_id)
    {
        $project_id = session('project_id');

        if(!$set = DrawingSet::where('id', $set_id)->where('project_id', $project_id)->first()){
            return back()
                ->withErrors('Record not found.')
                ->withInput();
        }

        $drawingSet = DrawingSet::where('project_id', $project_id)->select('id')->get();
        $allPlan = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->get();

        $drawingSetData = DrawingSet::where('project_id', $project_id)->get();

        $data = DrawingPlan::where('drawing_set_id', $set_id)->OrderBy('seq')->get();

        return view('project.step2DrawingPlan', compact('project_id', 'set', 'data', 'allPlan', 'drawingSetData'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $data = DrawingSet::find($request->id);

        return $data;
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
        if(!$drawing = DrawingSet::find($request->drawing_id)){
            return back()
            ->withErrors('Drawing set not found.')
            ->withInput();
        }

        $drawing->update([
            'name'              => $request->input('drawing_name'),
            'handover_key_id'   => $request->input('key_form'),
            'handover_es_id'    => $request->input('es_form'),
            'handover_form'     => $request->input('close_and_handover_form'), 
        ]);

        return back()->with(['success-message' => 'Record successfully updated.'])->withInput();   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        if(!$drawing = DrawingSet::where('id', $id)->where('project_id', session('project_id'))->first()){
            return back()
            ->withErrors('Record not found.')
            ->withInput();
        }   

        $drawingPlan = drawingPlan::where('drawing_set_id', $id)->get();

        foreach ($drawingPlan as $value) {
            File::delete(public_path('uploads/drawings/' . $value->file));
        }

        $drawing->drawingPlan()->delete();
        $drawing->delete();

        $set = DrawingSet::where('project_id', session('project_id'))->select('id')->orderBy('seq')->get();
        $seqStart = 0;
        
        foreach ($set as $set_id) {
            DrawingSet::where('id', $set_id["id"])
                ->where('project_id', session('project_id'))
                ->update(['seq' => ++$seqStart]);
        }

        return back()->with(['success-message' => 'Record successfully deleted.'])->withInput();
    }

    public function updateSort(Request $request){

        $id = session('project_id');

        $seqStart = 0;
        foreach ($request->seq as $key) {
            DrawingSet::where('id', $key)
                ->where('project_id', $id)
                ->update(['seq' => ++$seqStart]);
        }

    }


    public function storePlan(Request $request, $set_id){ 

        $lastSeq = 0;
        if($drawingSeq = DrawingPlan::where('drawing_set_id', $set_id)->orderby('seq', 'DESC')->first()){
            $lastSeq = $drawingSeq->seq;
        }

        // $type  = $request->input('type');
        
        // $level = null;
        // $block = null;
        // $phase = null;  

        // if($type == 'unit'){
        //     $level = $request->input('level');
        //     $block = $request->input('block');
        //     $phase = $request->input('phase');   
        // }



        if (count($request->input('image')) > 0) {
            foreach ($request->input('image') as $key => $value) {
                ++$lastSeq;
                
                ##format (phase_type_block_level_unit)
                if($request->input('type') != "custom"){
                    $image = explode('_', $value);

                    foreach ($image as $id => $nameImage) {
                        $image[$id] = ltrim($nameImage, " ");
                    }

                    $block = $image[1];
                    $level = $image[2];
                    $unit = $image[3];
                    $phase = $image[0];
                    $image_name = implode("_",$image);
                }else{
                    $block = null;
                    $level = null;
                    $unit = null;
                    $phase = null;
                    $image_name = $value;

                }

                $drawing_plan = DrawingPlan::create([
                    'drawing_set_id'        => $set_id,
                    'name'                  => $image_name,
                    'file'                  => $request->input('name_unique')[$key], 
                    'width'                 => $request->input('width')[$key],
                    'height'                => $request->input('height')[$key],
                    'seq'                   => $lastSeq,
                    'types'                 => $request->input('type'),
                    'block'                 => $block,
                    'level'                 => $level,
                    'unit'                  => $unit,
                    'phase'                 => $phase,
                ]);

                if ($drawing_plan->types != 'custom') { 
                    $this->setupInitialZone($drawing_plan);
                }
            }
        }

        $drawingSet = DrawingSet::where('project_id', session('project_id'))->select('id')->get();

        if(!$findDefault = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->where('default', 1)->first()){
            DrawingPlan::where('seq', 1)->whereIn('drawing_set_id', $drawingSet)->update(['default' => 1]);
        }

        return back()->with(['success-message' => 'New record successfully added.']);
    }

    public function viewPlan(Request $request){

        $data = DrawingPlan::find($request->input('id'));
        return $data;
    } 


    public function updatePlan(Request $request, $set_id){ 

        $rules = [
            'name'  => 'required',
        ];
        
        $validator = Validator::make($request->input(), $rules);  

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->with(['modal' => 'show'])
                ->withInput();
        }

        $phase = null;  
        $block = null;
        $level = null;
        $unit  = null;  

        if($request->input('type') != "custom" ){
            $name = explode('_', $request->input('name'));

            if(count($name) != 4){
                return back()
                    ->withErrors(["error-msg" => "format name for plan is incorrect."])
                    ->with(['modal' => 'show'])
                    ->withInput();
            }

                $phase = $name[0];   
                $block = $name[1];
                $level = $name[2];
                $unit = $name[3];   
        }

        if(!$DrawingPlan = DrawingPlan::where('drawing_set_id', $set_id)->where('id', $request->input('drawing_id'))->first()){

            return back()
                ->withErrors('Record not found.')
                ->withInput();
        }

        $DrawingPlan->update([
            'name'      => $request->input('name'),
            'types'     => $request->input('type'),
            'phase'     => $phase,
            'block'     => $block,
            'level'     => $level,
            'unit'      => $unit,
        ]);

        return back()->with(['success-message' => 'Record successfully updated.']);
    }

    public function destroyPlan($id){ 

        if(!$DrawingPlan = DrawingPlan::find($id)){
            return back()
            ->withErrors("Record not found.")
            ->withInput();
        }
        
        
        if(!$DrawingSet = DrawingSet::where('project_id', session('project_id'))->where('id', $DrawingPlan->drawing_set_id)->first())
        {
            return back()
            ->withErrors("Record not found.")
            ->withInput();
        }

        ##default image cant be deleted
        if($DrawingPlan->default == 1){
            return back()
            ->withErrors("Record not found.")
            ->withInput();
        }

        if(!$checkingPlan = DrawingPlan::where('file', $DrawingPlan->file)->where('id', '!=', $DrawingPlan->id)->first()){

            File::delete(public_path('uploads/drawings/'.  $DrawingPlan->file));
        }
            $DrawingPlan->delete();

        return back()->with(['success-message' => 'Record successfully deleted.']);

    }

    public function setDefault($plan_id, $set_id){

        if(!$draw_set = DrawingSet::find($set_id)){
            return back()
            ->withErrors('Record not found.')
            ->withInput();
        }

        if(!$plan = DrawingPlan::where('id', $plan_id)){
            return back()
            ->withErrors('Record not found.')
            ->withInput();
        }


        $drawingSet = DrawingSet::where('project_id', session('project_id'))->select('id')->get();

        DrawingPlan::where('default', 1)->whereIn('drawing_set_id', $drawingSet)->update(['default' => 0]);

        $plan->update([
            'default' => 1,
        ]);

        return back()->with(['success-message' => 'Record successfully updated.']);

    }

    public function duplicatePlan(Request $request, $set_id){

        $rules = [
            'plan'          => 'required',
            'number'        => 'required|numeric|min:1',
        ];

        $validator = Validator::make($request->input(), $rules);  

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->with(['modal' => 'show'])
                ->withInput();
        }

        $number = $request->input('number');
        $plan = DrawingPlan::find($request->input('plan'));

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
            $newPlan->unit = $newPlan->unit . '('. $current_copy .')';
            $newPlan->seq = $lastSeq;
            $newPlan->save();

            if ($newPlan->types != 'custom') { 
                $this->setupInitialZone($newPlan);
            }
        }

        return back()->with(['success-message' => "New record successfully added."]);

    }

    public function updateSortPlan(Request $request){

        $id = session('project_id');

        $seqStart = 0;

        foreach ($request->seq as $key) {
            DrawingPlan::where('id', $key)
                ->where('drawing_set_id', $request->input('set'))
                ->update(['seq' => ++$seqStart]);
        }

    }

    public function batchUpload(Request $request){

        $rules = [
            'drawing_set_id'        => 'required',
        ];

        $validator = Validator::make($request->input(), $rules);  

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        if (empty($request->file('batch'))) {
            return back()
                ->withErrors('Please upload your zip file.')
                ->withInput();
        }

        if(!$drawingSet = DrawingSet::find($request->input('drawing_set_id'))){
            return back()
                ->withErrors("Drawing set not found.")
                ->withInput();
        }

        $file = $request->file('batch');
        if($file->getClientOriginalExtension() != 'zip'){
            return back()
                ->withErrors("File upload must be in zip format")
                ->withInput();   
        }
        // return $file->getClientOriginalExtension();
        $temp_file = time().'.'.$file->getClientOriginalExtension();

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

        if($drawingSeq = DrawingPlan::where('drawing_set_id', $drawingSet->id)->orderby('seq', 'DESC')->first()){
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
                            
                            // return $folder;
                            $imageDimension = getImageSize($file . '/' . $commonUnitFile['basename']);
                            
                            Storage::move(('/uploads/' . $batch_folder . $folder) . '/' . $commonUnitFile['basename'], 'uploads/drawings/' . $name_unique );

                            $drawing_plan = DrawingPlan::create([
                                'drawing_set_id'        => $drawingSet->id,
                                'name'                  => $commonUnitFile["filename"],
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

                            $this->setupInitialZone($drawing_plan);
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
                            'drawing_set_id'        => $drawingSet->id,
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
        ## REMOVE FOR NOW ## FIND A REASON WHY HAVE THIS FUNCTION  ##
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

}
