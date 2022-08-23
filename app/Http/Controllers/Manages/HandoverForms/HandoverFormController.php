<?php

namespace App\Http\Controllers\Manages\Handoverforms;

use App\Entity\Project;
use App\Entity\HandOverFormList;
use App\Entity\HandOverFormSection;
use App\Entity\HandoverFormSectionItem;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;

class HandoverFormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('handover-forms.index');
    }

    public function indexData()
    {
        $form = HandOverFormList::where('project_id', session('project_id'))->where('status', true)->select(['handover_form_list.*']);

        return Datatables::of($form)
        
            ->addColumn('action', function ($form) {
                
                $button = edit_button(route('handover-form.edit', [$form->id]));

                $button .= '<a href="'. route('handover-form.clone', [$form->id]) .'" data-popup="tooltip" title="clone" data-placement="top" class="edit_button">
                                <i class="fa fa-copy fa-lg"></i>
                            </a>';

                $button .= '<a href="'. route('handover-form.show', [$form->id]) .'" data-popup="tooltip" title="View" data-placement="top" class="edit_button">
                                <i class="fa fa-wpforms fa-lg"></i>
                            </a>';

                
                return $button;
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
        return view('handover-forms.create');
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
            'name' => 'required',
            'item.*.*' => 'required',
        ];
        
        $message = [
            'item.*.*.required' => 'Name of item is required.',
        ];
        $this->validate($request, $rules, $message);
        
        $project = Project::find(session('project_id'));


        if(HandOverFormList::where('name', $request->input('name'))->where('project_id', $project->id)->first()){

            return back()->with(['warning-message' => 'Form name must be unique']);
        }


        $input = $request->input();
        $this->inserFormSectionDetails($input);

        return redirect()->route('handover-form.index')->with(['success-message' => 'New record successfully added.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(!$form = HandOverFormList::where('id', $id)->where('project_id', session('project_id'))->first()){
            return redirect()->route('handover-form.index')->with(['error-message' => 'Record not found.']);
        }

        return view('handover-forms.show', compact('form'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        if(!$form = HandOverFormList::where('id', $id)->where('project_id', session('project_id'))->first()){
            return redirect()->route('handover-form.index')->with(['error-message' => 'Record not found.']);
        }

        return view('handover-forms.edit', compact('form'));
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
            'name' => 'required',
            'item.*.*' => 'required',
        ];
        
        $message = [
            'item.*.*.required' => 'Name of item is required.',
        ];
        $this->validate($request, $rules, $message);
        
        $project = Project::find(session('project_id'));

        if($request->input('action_type') == 'normal') {
            HandOverFormList::where('id', $id)->update(['status' => 0]);
        }

        $input = $request->input();
        $this->inserFormSectionDetails($input);

        return redirect()->route('handover-form.index')->with(['success-message' => 'Record successfully updated.']);
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

    public function clone($id) {

        if(!$form = HandOverFormList::where('id', $id)->where('project_id', session('project_id'))->first()){
            return redirect()->route('handover-form.index')->with(['error-message' => 'Record not found.']);
        }

        $type = 'clone';

        return view('handover-forms.edit', compact('form', 'type'));
    }

    function inserFormSectionDetails($input){
        
        $form = HandOverFormList::create([
                'project_id'   => session('project_id'),
                'name'         => $input["name"],
                'description'  => $input["description"],
                'meter_reading'=> isset($input["meter_reading"]) ? true : false,
                'status'       => 1,
            ]);



        // if(isset($input["meter_reading"])){

        //     $section_meter = HandOverFormSection::create([

        //         'handover_form_list_id' => $form->id,
        //         'name' => 'meter_reading',
        //         'seq' => 1,
        //     ]);

        //     $meter_fields = ['electricity', 'water'];

        //     foreach ($meter_fields as $meter_type) {
        //         HandoverFormSectionItem::create([
        //             'handover_form_section_id'  => $section_meter->id,
        //             'name'                      => $meter_type,
        //             'quantity'                  => '1',
        //         ]);
        //     }

        // }


        // $i = isset($input["meter_reading"]) ? 2 : 1;
        
        $i = 1;
        foreach ($input['section'] as $sectionKey => $sectionVal) {

            $section = HandOverFormSection::create([

                'handover_form_list_id' => $form->id,
                'name' => $sectionVal,
                'seq' => $i++,
            ]);

            foreach ($input['item'][$sectionKey] as $itemKey => $itemVal) {
                HandoverFormSectionItem::create([
                    'handover_form_section_id'  => $section->id,
                    'name'                      => $itemVal,
                    'quantity'                  => $input['quantity'][$sectionKey][$itemKey],
                ]);

            }
        }
    }
}
