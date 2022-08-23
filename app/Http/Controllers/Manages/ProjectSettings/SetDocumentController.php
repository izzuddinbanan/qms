<?php

namespace App\Http\Controllers\Manages\ProjectSettings;

use Session;
use App\Entity\Project;
use App\Entity\Document;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class SetDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        destroy_route_session();
        set_curret_route(route('set-document.index'));

        $docs = Document::where('client_id', role_user()->client_id)->orderBy('name')->get();

        $docs->each(function ($record) {
            $record['selected'] = $record->document->where('id', session('project_id'))
                ->count() ? 1 : 0;
            unset($record['document']);
        });

        return view('project-settings.set-documents.index', compact('docs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        set_route_session(route('set-document.index'));

        return redirect()->route('document.create');
        
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
                'doc'          => 'required|array|min:1',
            ];

            $message = [
                'doc.required' => "Please select at least 1 Document.",
            ];

            $this->validate($request, $rules, $message);

            $doc_arr = array_unique($request->input('doc'));

            if(!$project = Project::where('client_id', role_user()->client_id)->where('id', session('project_id'))->first()){
                return redirect()->route('set-document.index')->withErrors(trans('main.record-not-found'));
            }

            $project->document()->detach();
            $project->document()->attach($doc_arr);


        } catch (ValidationException $e) {
            return redirect(route('set-document.index'))
                ->withErrors($e->getErrors())
                ->withInput();
        }
        return redirect(route('set-document.index'))
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
