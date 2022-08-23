<?php

namespace App\Http\Controllers\Manages\ProjectSettings;

use App\Entity\DrawingPlan;
use App\Entity\DrawingSet;
use App\Entity\FormGroup;
use App\Entity\HandOverFormList;
use App\Entity\HandOverMenu;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class SetDrawingSetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $role_user = role_user();

        $forms = HandOverFormList::where('status', true)->where('project_id', session('project_id'))->get();

        $drawing_set = DrawingSet::where('project_id', session('project_id'))->get();
        $digital_forms = FormGroup::where('client_id', $role_user->client_id)->get();

        $es = HandOverMenu::where('project_id', session('project_id'))->where('original_name', 'es')->first();
        $key = HandOverMenu::where('project_id', session('project_id'))->where('original_name', 'key')->first();

        return view('project-settings.set-drawing-sets.index', compact('drawing_set', 'forms', 'es', 'key', 'digital_forms'));
    }

    public function indexData ()
    {
        $drawing_sets = DrawingSet::where('project_id', session('project_id'))->select(['drawing_sets.id', 'drawing_sets.seq', 'drawing_sets.name']);

        return Datatables::of($drawing_sets)
            ->addColumn('action', function ($drawing_sets) {
                
                $button = '<a href="#" data-popup="tooltip" title="'. trans('main.edit') .'" data-placement="top" onclick="return editForm(' . $drawing_sets->id .')" class="tooltip-show"><i class="fa fa-pencil-square-o fa-lg"></i></a>';
                
                $button .= delete_button(route('set-drawing-set.destroy', [$drawing_sets->id]));
                
                return $button;
            })
            ->addColumn('total-drawing', function ($drawing_sets) {
                
                return '<a href="'. route('set-drawing-plan.show', [$drawing_sets->id]) .'"><button type="button" class="btn btn-primary btn-xs">view <span class="badge badge-'. ($drawing_sets->drawingPlan->count() == 0 ? 'danger' : 'primary') .'">'. $drawing_sets->drawingPlan->count() .'</span></button></a>';
            })
            ->rawColumns(['action', 'total-drawing'])
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

        try {

            $rules = [
                'drawing_name'  => 'required',
            ];

            $message = [];

            $this->validate($request, $rules, $message);

            $lastSeq = 0;
            if($drawingSeq = DrawingSet::where('project_id', session('project_id'))->orderby('seq', 'DESC')->first()){
                $lastSeq = $drawingSeq->seq;
            }

            DrawingSet::create([
                'project_id'        => session('project_id'),
                'seq'               => ++$lastSeq,
                'name'              => $request->input('drawing_name'),
                'handover_key_id'   => $request->input('key_form'),
                'handover_es_id'    => $request->input('es_form'),
                'handover_form'     => $request->input('close_and_handover_form'),
            ]);



        } catch (ValidationException $e) {
            return redirect(route('set-drawing.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return back()->with(['success-message' => 'New record successfully added.']);


        
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
        try {

            $rules = [
                'drawing_name'  => 'required',
                'seq'           => 'required|integer|min:1',
            ];

            $message = [];

            $this->validate($request, $rules, $message);

            if(!$drawing = DrawingSet::where('id', $request->drawing_id)->where('project_id', session('project_id'))->first()){
                return back()->withErrors('Drawing set not found.');
            }


            if($drawing->seq != $request->input('seq')) {

                $maxSeq = DrawingSet::where('project_id', session('project_id'))->orderby('seq', 'DESC')->first();

                $new_seq = $request->input('seq') <= $maxSeq->seq ? $request->input('seq') : $maxSeq->seq;


                if($drawing->seq > $request->input('seq')) {

                    $drawSet = DrawingSet::where('project_id', session('project_id'))->where('seq', '>=', $new_seq)->where('seq', '<', $drawing->seq)->get();

                    foreach ($drawSet as $key => $value) {
                        DrawingSet::where('id', $value->id)->update(['seq' => ++$value->seq]);
                    }

                }else {

                    $drawSet = DrawingSet::where('project_id', session('project_id'))->where('seq', '<=', $new_seq)->where('seq', '>', $drawing->seq)->get();
                    foreach ($drawSet as $key => $value) {
                        DrawingSet::where('id', $value->id)->update(['seq' => --$value->seq]);
                    }

                }

                $drawing->update([
                    'seq'      => $new_seq,
                ]);

            }

            $drawing->update([
                'name'              => $request->input('drawing_name'),
                'handover_key_id'   => $request->input('key_form'),
                'handover_es_id'    => $request->input('es_form'),
                'handover_form'     => $request->input('close_and_handover_form'),
            ]);

        } catch (ValidationException $e) {
            return redirect(route('set-drawing.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return back()->with(['success-message' => 'Record successfully updated.'])->withInput();   
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

            if ($drawing_sets = DrawingSet::find($id)) {

                DrawingPlan::where('drawing_set_id', $id)->delete();

                $drawing_sets->delete();

                $drawingSet = DrawingSet::where('project_id', session('project_id'))->orderBy('seq')->get();

                $i = 1;
                foreach ($drawingSet as $key => $value) {
                    DrawingSet::where('id', $value->id)->update(['seq' => $i++]);
                }

                return response()->json(['status' => 'ok']);

            }

        }
        return \Response::json(['status' => 'fail']);
    }

}
