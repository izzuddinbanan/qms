<?php

namespace App\Http\Controllers\Manages\ProjectSettings;

use App\Entity\Project;
use App\Entity\FormGroup;
use App\Entity\GroupForm;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SetInspectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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

        return view('project-settings.set-inspections.index', compact('forms', 'groupForm'));
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

            $rules = [];

            $message = [];

            $this->validate($request, $rules, $message);

            $form = $request->input('form');
            $group = $request->input('group');

            if(!$project = Project::where('client_id', role_user()->client_id)->where('id', session('project_id'))->first()){
                return redirect()->route('set-form.index')->withErrors(trans('main.record-not-found'));
            }

            $project->digitalform()->detach();
            $project->digitalform()->attach($form);

            $project->groupDigitalform()->detach();
            $project->groupDigitalform()->attach($group);

        } catch (ValidationException $e) {
            return redirect(route('set-form.index'))
                ->withErrors($e->getErrors())
                ->withInput();
        }
        return redirect(route('set-inspection.index'))
            ->withSuccess(trans('main.success-update'));
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
