<?php

namespace App\Http\Controllers;

use Response;
use App\Entity\Document;
use App\Entity\DocumentVersion;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class DocumentController extends Controller
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
        return view('documents.index');
    }

    public function indexData(){

        $doc = Document::where('client_id', role_user()->client_id)->with('activeVersion')->select(['documents.*']);

        return Datatables::of($doc)
            ->addColumn('url_file', function ($doc) {
                
                return $label = '<a href="' . url('uploads/documents/' . $doc->activeVersion->file) . '" target="_blank"><span class="label label-info"><i class="fa fa-download"></i> Download</span></a>';
                
            })
            ->addColumn('action', function ($doc) {
                
                $button = edit_button(route('document.edit', [$doc->id]));
                $button .= delete_button(route('document.destroy', [$doc->id]));
                
                return $button;
            })
            ->addColumn('view-version', function ($doc) {
                
                return $label = '<a href="' . route('document.show', [$doc->id]) . '"><button class="btn btn-info">View All Version</button></a>';
                
            })
            ->rawColumns(['action', 'url_file', 'view-version'])
            ->make(true);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('documents.create');
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
                'name'          => 'required|unique:documents,name,null,id,deleted_at,NULL',
                'file'          => 'required|file|mimes:pdf|max:' . config('global-settings.filesize'),
            ];

            $message = [
            ];

            $this->validate($request, $rules, $message);


            $doc = Document::create([
                'name'        => $request->input('name'),
                'client_id'   => role_user()->client_id,
            ]);

            $version = DocumentVersion::where('document_id', $doc->id)->withTrashed()->count();
            
            DocumentVersion::create([
                'document_id' => $doc->id,
                'file'        => \App\Processors\SaveFileProcessor::make($request->file('file'))->execute(),
                'version'     => ++$version,
                'publish'     => true,
                'created_by'  => \Auth::user()->id,
                'updated_by'  => \Auth::user()->id,
            ]);

        } catch (ValidationException $e) {
            return redirect(route('document.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        if(get_route_session()){
            
            destroy_route_session();

            return redirect(route('set-document.index'))
                ->withSuccess(trans('main.success-add'));    
        }
        return redirect(route('document.index'))
            ->withSuccess(trans('main.success-add'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(!$doc = Document::where('client_id', role_user()->client_id)->where('id', $id)->first()){
            return redirect()->route('document.index')->withErrors(trans('main.record-not-found'));
        }

        return view('documents.show', compact('doc'));
    }

    public function showData($id){

        $version = DocumentVersion::where('document_id', $id)->orderBy('created_at', 'DESC')->select(['version', 'file', 'publish', 'created_at']);

        return Datatables::of($version)
            ->addColumn('uploaded_at', function ($version) {
                
                return $version->created_at->toDateString();
                
            })
            ->addColumn('publish-label', function ($version) {
                
                return '<span class="label label-'. ($version->publish ? 'info' : 'danger') .'">'. ($version->publish ? 'yes' : 'no') .'</span>';
                
            })
            ->addColumn('action', function ($version) {
                
                return $label = '<a href="' . url('uploads/documents/' . $version->file) . '" target="_blank"><span class="label label-info"><i class="fa fa-download"></i> Download</span></a>';
                
            })
            ->rawColumns(['uploaded_at', 'publish-label', 'action'])
            ->make(true);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!$doc = Document::where('client_id', role_user()->client_id)->where('id', $id)->first()){
            return redirect()->route('document.index')->withErrors(trans('main.record-not-found'));
        }

        return view('documents.edit', compact('doc'));
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
                'name'          => 'required|unique:documents,name,'. $id .',id,deleted_at,NULL',
            ];


            $message = [
            ];

            $this->validate($request, $rules, $message);

            if(!$doc = Document::where('client_id', role_user()->client_id)->where('id', $id)->first()){

                return redirect()->route('document.index')->withErrors(trans('main.record-not-found'));
            }

            $doc->update([
                'name'        => $request->input('name'),
            ]);

            if($request->hasFile('file')){

                // \App\Processors\DeleteFileProcessor::make(public_path('uploads/documents/' . $doc->file))->execute();

                // $doc->forceFill(['file' => \App\Processors\SaveFileProcessor::make($request->file('file'))->execute()])->save();
                DocumentVersion::where('document_id', $doc->id)->where('publish', true)->update(['publish' => false]);

                $version = DocumentVersion::where('document_id', $doc->id)->withTrashed()->count();

                DocumentVersion::create([
                    'document_id' => $doc->id,
                    'file'        => \App\Processors\SaveFileProcessor::make($request->file('file'))->execute(),
                    'version'     => ++$version,
                    'publish'     => true,
                    'created_by'  => \Auth::user()->id,
                    'updated_by'  => \Auth::user()->id,
                ]);

            }

        } catch (ValidationException $e) {
            return redirect(route('document.edit', [$doc->id]))
                ->withErrors($e->getErrors())
                ->withInput();
        }
        return redirect(route('document.index'))
            ->withSuccess(trans('main.success-update'));
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

            if (Document::where('client_id', role_user()->client_id)->where('id', $id)->exists()) {

                $doc = Document::find($id);

                $version = DocumentVersion::where('document_id', $id)->get();

                foreach ($version as $key => $value) {
                    
                    \App\Processors\DeleteFileProcessor::make(public_path('uploads/documents/' . $value->file))->execute();
                
                }

                $doc->version()->delete();
                $doc->delete();
                
                return response()->json(['status' => 'ok']);
            }

        }
        return Response::json(['status' => 'fail']);
    }
}
