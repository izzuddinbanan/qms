<?php

namespace App\Http\Controllers;

use App\Entity\FormGroup;
use App\Entity\FormGroupStatus;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class FormStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        if(!$formGroup = FormGroup::where('id', $id)->where('client_id', role_user()->client_id)->first()){
            return redirect()->route('form.index')->withErrors(trans('main.record-not-found'));
        }
        return view('form.status.index', compact('formGroup'));
    }

    public function indexData($id){

        $status = FormGroupStatus::where('form_group_id', $id)->select(['name', 'id', 'color_code']);

        return Datatables::of($status)
        
            ->addColumn('color', function ($status) {
                return '<i class="icon-droplet" style="color:'. $status->color_code .' !important"></i>';
            })
            ->addColumn('action', function ($status) use ($id) {
                
                $button = edit_button(route('form-status.edit', [$id, $status->id]));
                $button .= delete_button(route('form-status.destroy', [$status->id]));
                
                return $button;
            })
            ->rawColumns(['action', 'color'])
            ->make(true);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        if(!$formGroup = FormGroup::where('id', $id)->where('client_id', role_user()->client_id)->first()){
            return redirect()->route('form.index')->withErrors(trans('main.record-not-found'));
        }

        return view('form.status.create', compact('formGroup'));
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
                'name'          => 'required',
                'colorcode'     => 'required',
            ];

            $message = [];

            $this->validate($request, $rules, $message);

            if(!$formGroup = FormGroup::where('id', $request->input('form_id'))->where('client_id', role_user()->client_id)->first()){
                return redirect()->route('form.index')->withErrors(trans('main.record-not-found'));
            }

            if(FormGroupStatus::where('form_group_id', $formGroup->id)->where('name', $request->input('name'))->first()){
                return redirect()->route('form-status.create', [$formGroup->id])->withErrors(trans('main.alert-unique', ['field' => 'status name']))->withInput();
            }

            FormGroupStatus::create([
                'name'           => $request->input('name'),
                'color_code'     => $request->input('colorcode'),
                'form_group_id'  => $formGroup->id,
            ]);


        } catch (ValidationException $e) {
            return redirect(route('form-status.create', [$formGroup]))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('form-status.index', [$formGroup]))
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
    public function edit($id, $status_id)
    {
        if(!$formGroup = FormGroup::where('id', $id)->where('client_id', role_user()->client_id)->first()){
            return redirect()->route('form.index')->withErrors(trans('main.record-not-found'));
        }

        if(!$formGroupStatus = FormGroupStatus::where('form_group_id', $formGroup->id)->where('id', $status_id)->first()){
            return redirect()->route('form-status.index', [$formGroup->id])->withErrors(trans('main.record-not-found'));
        }


        return view('form.status.edit', compact('formGroupStatus', 'formGroup'));


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
                'name'          => 'required',
                'colorcode'     => 'required',
            ];

            $message = [];

            $this->validate($request, $rules, $message);

            if(!$formGroup = FormGroup::where('id', $request->input('form_id'))->where('client_id', role_user()->client_id)->first()){
                return redirect()->route('form.index')->withErrors(trans('main.record-not-found'));
            }

            if(FormGroupStatus::where('form_group_id', $formGroup->id)->where('name', $request->input('name'))->where('id', '!=', $id)->first()){
                return redirect()->route('form-status.create', [$formGroup->id])->withErrors(trans('main.alert-unique', ['field' => 'status name']))->withInput();
            }


            if(!$formStatus = FormGroupStatus::where('form_group_id', $formGroup->id)->where('id', $id)->first()){
                return redirect()->route('form-status.index', [$formGroup->id])->withErrors(trans('main.record-not-found'));
            }

            $formStatus->update([
                'name'           => $request->input('name'),
                'color_code'     => $request->input('colorcode'),
            ]);


        } catch (ValidationException $e) {
            return redirect(route('form-status.index', [$formGroup]))
                ->withErrors($e->getErrors())
                ->withInput();
        }

        return redirect(route('form-status.index', [$formGroup]))
            ->withSuccess(trans('main.success-update'));
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
