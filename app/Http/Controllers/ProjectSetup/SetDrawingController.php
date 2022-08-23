<?php

namespace App\Http\Controllers\ProjectSetup;

use App\Entity\DrawingSet;
use App\Entity\HandOverFormList;
use App\Entity\HandOverMenu;
use App\Entity\Language;
use App\Entity\Project;
use App\Entity\FormGroup;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class SetDrawingController extends Controller
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

        $client = $project->client;

        $digital_forms = FormGroup::where('client_id', $client->id)->get();

        $forms = HandOverFormList::where('status', true)->where('project_id', $id)->get();

        $es = HandOverMenu::where('project_id', session('project_id'))->where('original_name', 'es')->first();
        $key = HandOverMenu::where('project_id', session('project_id'))->where('original_name', 'key')->first();

        $data = DrawingSet::where('project_id', $id)
                            ->with('drawingPlan')
                            ->orderBy('seq')->get();
                            
        return view('project.set-drawing.index', compact('id', 'data', 'language', 'forms', 'es', 'key', 'project', 'digital_forms'));
    }

    public function indexData(){

        $drawSet = DrawingSet::where('project_id', session('project_id'))->orderBy('seq')->select(['id', 'name', 'seq', 'count']);

        return Datatables::of($drawSet)
            ->addColumn('total-count', function ($drawSet) {
                
                $button = '<button>Manage Drawing Plan</button>';

                $button = '<a href="'. route('step2.show', [$drawSet->id] ) .'">
                                <button class="btn btn-info">Manage Drawing Plan</button>
                            </a>';
                
                return $button;
            })
            ->addColumn('action', function ($drawSet) {
                
                $button = edit_button(route('document.edit', [$drawSet->id]));
                $button .= delete_button(route('document.destroy', [$drawSet->id]));
                
                return $button;
            })
            ->rawColumns(['action', 'total-count'])
            ->make(true);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('project.set-drawing.create');
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
