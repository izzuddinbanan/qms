<?php

namespace App\Http\Controllers\Manages\ProjectSettings;

use Validator;
use App\Entity\Language;
use App\Entity\Project;
use App\Entity\DrawingSet;
use App\Entity\DrawingPlan;
use App\Entity\LocationPoint;
use App\Entity\FormGroup;
use App\Entity\GroupForm;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;

class SetLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        DrawingPlan::get();
        // $data = DrawingSet::where('project_id', session('project_id'))->with('drawingPlanOrder')->orderBy('seq')->get();
        // return view('project.set-location.index', compact('data'));

        $id = session('project_id');
        
        $data = DrawingSet::where('project_id', $id)->orderBy('seq')->get();
        $drawingSet = DrawingSet::where('project_id', $id)->select('id')->get();
        $default = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->where('default', 1)->first();

        $project = Project::find($id);

        $language = Language::get();

        $langSetup = explode(',', $project->language_id);


        $forms = FormGroup::where('client_id', role_user()->client_id)->orderBy('name')->get();
        
        $forms->each(function ($record) {
            $record['selected'] = $record->projects->where('id', session('project_id'))
                ->count() ? 1 : 0;
            unset($record['projects']);
        });

        $groupForm = GroupForm::where('client_id', role_user()->client_id)->orderBy('name')->get();

        $groupForm->each(function ($record) {
            $record['selected'] = $record->projectForm->where('id', session('project_id'))
                ->count() ? 1 : 0;
            unset($record['projects']);
        });

        $listDrawingSet = DrawingSet::where('project_id', $id)->get();

        return view('project-settings.set-locations.index', compact('id', 'data', 'default', 'language', 'langSetup', 'forms', 'groupForm', 'listDrawingSet'));
        
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
    public function update(Request $request)
    {
        $location = LocationPoint::where('id', $request->input('id'))
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
    public function destroy(Request $request)
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

    public function duplicate(Request $request)
    {   

        $location = LocationPoint::where('drawing_plan_id', $request->input('drawing_plan_id_location'))->where('name', '!=', 'Other')->get();

        foreach (array_unique($request->input('drawingPlanLocation')) as $selectedPlan) {

            foreach ($location as $valueLoc) {

                $location_clone = LocationPoint::create([
                    'name'              =>  $valueLoc->name,
                    'drawing_plan_id'   =>  $selectedPlan,
                    'status_id'         =>  1,
                    'points'            =>  $valueLoc->points,
                    'color'             =>  $valueLoc->color,
                ]);

                $last = LocationPoint::where('drawing_plan_id', $selectedPlan)->withTrashed()->count(); 

                $now = \Carbon\Carbon::now('Asia/Kuala_Lumpur');
                $format = date("dmy", strtotime($now));

                $unique_ref = $format . '-P' . $location_clone->drawing_plan_id . 'L' . $location_clone->id . '-R' . $last; 

                $location_clone->forcefill(['reference' => $unique_ref])->save();

            }

        }

        return back()->with(['success-message' => 'New Record Successfully added.']);
    }

    public function listFormSelect(Request $request){


        $form = array();
        $Groupform = array();
        $form = array();
        $gForm = array();
        $form_id = array();
        $list_form_id = array();

        if($request->input('form_id')){

            foreach ($request->input('form_id') as $key => $value) {
                
                $id = substr($value, 2);
                $type = substr($value, 0,1);

                if($type == 's'){
                    $form[] = $id;
                }else{
                    $gForm[] = $id;
                }

            }

            $group = GroupForm::whereIn('id', $gForm)->get();

            foreach ($group as $key => $value) {

                $temp_data = $value->form()->select('id')->get();
                foreach ($temp_data as $key => $value) {
                    $form_id[] = $value->id;
                }
            }
            $single = array_unique($form_id);
            $list_form_id = array_merge($single, $form);
            $list_form_id = array_unique($list_form_id);

        }

        return $data = FormGroup::whereIn('id', $list_form_id)->select('name')->get();

    }

    public function listDrawingPlan(Request $request){

        return DrawingPlan::where('drawing_set_id', $request->input('drawing_set_id'))->whereDoesntHave('location', function (Builder $query) {
                $query->where('name', '!=', 'Other');
            })->get();

    }
}
