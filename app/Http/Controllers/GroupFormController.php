<?php

namespace App\Http\Controllers;

use Response;
use App\Entity\GroupForm;
use App\Entity\FormGroup;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class GroupFormController extends Controller
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

        return view('group-form.index');
    }

    public function indexData(){

        $group = GroupForm::where('client_id', role_user()->client_id)->select(['id', 'name', 'total']);

        return Datatables::of($group)
            ->addColumn('total-label', function ($group) {
                
                return $label = '<span class="label label-info">'. $group->total .'</span>';
                
            })  
            ->addColumn('action', function ($group) {
                
                $button = edit_button(route('group-form.edit', [$group->id]));
                $button .= delete_button(route('group-form.destroy', [$group->id]));
                return $button;
            })
            ->rawColumns(['action', 'total-label'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $forms = FormGroup::where('client_id', role_user()->client_id)->orderBy('name')->get();
        return view('group-form.create', compact('forms'));
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
                'name'          => 'required|unique:group_form,name,null,id,deleted_at,NULL',
                'form'          => 'required|array|min:1',
            ];

            $message = [
                'form.required' => "Please select at least 1 form.",
            ];

            $this->validate($request, $rules, $message);

            $form_arr = $request->input('form');
            $form = implode(',', array_unique($form_arr)); //to resolve duplicate value when send using bootstrap dual listbox

            $group = GroupForm::create([
                'name'        => $request->input('name'),
                'client_id'   => role_user()->client_id,
                'total'       => count($form_arr),
                'created_by'  => \Auth::user()->id,
                'updated_by'  => \Auth::user()->id,
            ]);

            $group->form()->attach($form_arr);


        } catch (ValidationException $e) {
            return redirect(route('group-form.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }
        return redirect(route('group-form.index'))
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

        if(!$group = GroupForm::where('client_id', role_user()->client_id)->where('id', $id)->first()){
            return redirect()->route('group-form.index')->withErrors(trans('main.record-not-found'));
        }

        $forms = FormGroup::where('client_id', role_user()->client_id)->orderBy('name')->get();

        $forms->each(function ($record) use ($id) {
            $record['selected'] = $record->form->where('id', $id)
                ->count() ? 1 : 0;
            unset($record['form']);
        });

        return view('group-form.edit', compact('forms', 'group'));
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
                'name'          => 'required|unique:group_form,name,'. $id .',id,deleted_at,NULL',
                'form'          => 'required|array|min:1',
            ];

            $message = [
                'form.required' => "Please select at least 1 form.",
            ];

            $this->validate($request, $rules, $message);

            $form_arr = $request->input('form');
            // $form = implode(',', array_unique($form_arr)); //to resolve duplicate value when send using bootstrap dual listbox

            if(!$group = GroupForm::where('client_id', role_user()->client_id)->where('id', $id)->first()){
                return redirect()->route('group-form.index')->withErrors(trans('main.record-not-found'));
            }

            $group->update([
                'name'        => $request->input('name'),
                'total'       => count($form_arr),
                'updated_by'  => \Auth::user()->id,
            ]);

            $group->form()->detach();
            $group->form()->attach($form_arr);


        } catch (ValidationException $e) {
            return redirect(route('group-form.create'))
                ->withErrors($e->getErrors())
                ->withInput();
        }
        return redirect(route('group-form.index'))
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

            if (GroupForm::where('id', $id)->exists()) {

                $group = GroupForm::find($id);

                $group->form()->detach();
                $group->delete();

                return response()->json(['status' => 'ok']);
            }

        }
        return Response::json(['status' => 'fail']);
    }
}
