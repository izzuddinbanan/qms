<?php

namespace App\Http\Controllers\Manages\PropertyUnits;

use App\Entity\DrawingPlan;
use App\Entity\DrawingSet;
use App\Entity\RoleUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class PropertyUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('property-unit.index');
    }

    public function indexData(){

        $drawingSet = DrawingSet::where('project_id', session('project_id'))->select('id')->get();
        $unit = DrawingPlan::whereIn('drawing_set_id', $drawingSet)->where('types', 'unit')->with(['unitOwner'])->select(['drawing_plans.*']);
        return Datatables::of($unit)
            ->addColumn('owner', function ($unit) {
                
                if($unit->unitOwner){
                    return $unit->unitOwner->name;// .'<br>('. $unit->unitOwner->email .')' ;
                }
                return '<label class="label label-success">VACANT</label>';

            })
            ->addColumn('action', function ($unit) {
                
                $button = '';
                $button .= '<a href="'. ($unit->unitOwner ? route('property-unit.edit', [$unit->id]) : 'javascript:void()') .'" data-popup="tooltip" title="'. trans('main.edit') .'" data-placement="top" class="edit_button" style="color: '. ($unit->unitOwner ? '' : 'grey') .'"><i class="fa fa-pencil-square-o fa-lg"></i></a>';

                $button .= '<a href="'. ($unit->unitOwner ? route('property-unit.owner-info', [$unit->id]) : 'javascript:void()') .'" data-popup="tooltip" title="Owner Info" data-placement="top" class="edit_button" style="color: '. ($unit->unitOwner ? '' : 'grey') .'"><i class="fa fa-user fa-lg"></i></a>';

                return $button;
                return '<a href="'. ($unit->unitOwner ? route('key-access.show', [$unit->id]) : 'javascript:void()') .'" data-popup="tooltip" title="'. trans('main.view') .'" data-placement="top" class="edit_button" style="color: '. ($unit->unitOwner ? '' : 'grey') .'"><i class="fa fa-eye fa-lg"></i></a>';

            })
            ->rawColumns(['owner', 'action', 'item-total'])
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
        //
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
        $drawingSet = DrawingSet::where('project_id', session('project_id'))->select('id')->get();

        $unit = DrawingPlan::where('id', $id)->where('types', 'unit')->whereIn('drawing_set_id', $drawingSet)->first();
        return view('property-unit.edit', compact('unit'));
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
        $drawingSet = DrawingSet::where('project_id', session('project_id'))->select('id')->get();

        if(!$unit = DrawingPlan::where('id', $id)->where('types', 'unit')->whereIn('drawing_set_id', $drawingSet)->first()){
            return redirect()->route('property-unit.index')->with(['warning-message' => 'Record not found']);
        }
        $unit->update([
            'unit_id'           => $request->input('unit_id'),
            'car_park'          => $request->input('car_park'),
            'access_card'       => $request->input('access_card'),
            'key_fob'           => $request->input('key_fob'),
            'spa_date'          => $request->input('spa_date'),
            'vp_date'           => $request->input('vp_date'),
            'dlp_expiry_date'   => $request->input('dlp_expiry_date'),
        ]);

        return redirect()->route('property-unit.index')->with(['success-message' => 'Record successfully updated.']);

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

    public function ownerInfo($id){
        $drawingSet = DrawingSet::where('project_id', session('project_id'))->select('id')->get();

        $unit = DrawingPlan::where('id', $id)->where('types', 'unit')->whereIn('drawing_set_id', $drawingSet)->first();
        return view('property-unit.owner-info', compact('unit'));
    }
}
