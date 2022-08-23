<?php
namespace App\Http\Controllers\ProjectSetup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Entity\Form;
use App\Entity\RoleUser;
use App\Entity\FormGroup;

class Step8Controller extends Controller
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
        $role_user = RoleUser::find(session('role_user_id'));
        
        $forms = FormGroup::where('client_id', $role_user->client_id)->get();
        
        $forms->each(function ($record) use ($id) {
            $record['checked'] = $record->projects->where('id', $id)
                ->count() ? 1 : 0;
            unset($record['projects']);
        });
        
        return view('project.step8', compact('id', 'forms'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $client_id = RoleUser::find(session('role_user_id'))->client_id;
        
        $rules = [
            'form_group_id' => 'required|exists:form_groups,id,client_id,' . $client_id
        ];
        
        $this->validate($request, $rules);
        
        try {
            $project_id = session('project_id');
            $form = FormGroup::find($request->input('form_group_id'));
            
            $form->projects()->attach($project_id);
            
            return $form;
        } catch (\Exception $e) {
            return [
                'errors' => 'fail to store record'
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $project_id = session('project_id');
            $form = FormGroup::find($id);
            
            $form->projects()->detach($project_id);
            
            return $form;
        } catch (\Exception $e) {
            return [
                'errors' => 'fail to delete record'
            ];
        }
    }
}
