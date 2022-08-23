<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Entity\Form;
use App\Entity\RoleUser;
use App\Entity\Attribute;
use App\Entity\FormGroup;
use App\Entity\FormVersion;
use Illuminate\Http\Request;
use App\Entity\FormSubmission;
use App\Entity\Submission;
use App\Entity\GeneralStatus;
use App\Entity\FormGroupStatus;

class FormController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('isSuperUser');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $param = request()->query();
        $search = app('request')->input('search');
        $client_id = RoleUser::find(session('role_user_id'))->client_id;
        
        $data = FormGroup::where('client_id', $client_id)->search($search, null, true, true)
            ->sortable()
            ->paginate(20);
        
        return view('form.index', compact('data', 'search'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'file' => 'required|array',
            'file.*.name' => 'required',
            'file.*.height' => 'required',
            'file.*.width' => 'required'
        
        ];
        
        $this->validate($request, $rules);
        
        $role_user = RoleUser::find(session('role_user_id'));
        
        try {
            \DB::beginTransaction();
            
            $form_group = FormGroup::create([
                'name' => $request->input('name'),
                'client_id' => $role_user->client_id
            ]);
            
            FormGroupStatus::create([
                'form_group_id' => $form_group->id,
                'fix_label'     => 'open',
                'name'     => 'open',
                'color_code'    => '#FFDE00',
            ]);

            FormGroupStatus::create([
                'form_group_id' => $form_group->id,
                'fix_label'     => 'closed',
                'name'          => 'closed',
                'color_code'    => '#FF0000',
            ]);
            

            $version = $form_group->versions()->create([
                'version' => 1,
                'status' => FormVersion::STATUS_PENDING
            ]);
            
            foreach ($request->input('file') as $key => $val) {
                $version->forms()->create([
                    'file' => $val['name'],
                    'height' => $val['height'],
                    'width' => $val['width']
                ]);
            }
            
            \DB::commit();
            
            return [
                'success-message' => 'New record successfully added!'
            ];
        } catch (\Exception $e) {
            \DB::rollBack();
            return [
                'fail-message' => 'Fail to store form'
            ];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return FormGroup::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required'
        ];
        
        $this->validate($request, $rules);
        
        $form = FormGroup::find($id)->update([
            'name' => $request->input('name')
        ]);
        
        return redirect('form')->with('success-message', 'Record update successful!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($data = FormGroup::find($id)) {
            $data->delete();
            
            return redirect('form')->with('success-message', 'Form already remove!');
        } else {
            return redirect('form')->with('fail-message', 'Form not found!');
        }
    }

    public function submit(Request $request)
    {
        $rules = [
            'form_group_id' => 'required',
            'location_id' => 'required|exists:locations,id',
            'input' => 'required|array',
            'input.*.location_id' => 'required|exists:form_attribute_locations,id',
            'input.*.value' => 'required'
        ];
        
        $this->validate($request, $rules);
        
        $location_id = $request->input('location_id');
        $last = Submission::where('location_id', $location_id)->count() + 1;
        $date = Carbon::now()->format('dmy');
        $form_group_id = $request->input('form_group_id');
        $reference_no = "$date-F$form_group_id-L$location_id-R$last";
        
        try {
            \DB::beginTransaction();
            
            $submission = Submission::create([
                'reference_no' => $reference_no,
                'location_id' => $location_id,
                'user_id' => auth()->user()->id,
                'status_id' => GeneralStatus::where([
                    'name' => 'pending',
                    'type' => 'submission'
                ])->first()->id
            ]);
            
            $value = $request->input('input');
            foreach ($val as $input) {
                $submission->formGroup()->create([
                    'form_group_id' => $request->input('form_group_id'),
                    'form_attribute_location_id' => $input['location_id'],
                    'value' => $input['value']
                ]);
            }
            
            \DB::commit();
            
            return $form_submission;
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }
}
