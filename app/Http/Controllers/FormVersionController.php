<?php
namespace App\Http\Controllers;

use App\Entity\FormGroup;
use App\Entity\Role;
use App\Entity\Attribute;
use App\Entity\FormVersion;
use App\Entity\FormSection;

class FormVersionController extends Controller
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
    public function index($id)
    {
        $form = FormGroup::find($id);
        $data = FormVersion::where('form_group_id', $id)->sortable()
            ->orderBy('version', 'desc')
            ->paginate(20);
        
        foreach ($data as $version) {
            $version->append('status_name');
        }
        
        return view('form.version', compact('form', 'data'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $form_version = FormVersion::with([
            'formGroup',
            'forms.formAttributes.locations',
            'forms.formAttributes.attribute',
            'forms.formAttributes.roles'
        ])->find($id);
        
        $option = Attribute::all();
        $roles = Role::whereIn('name', [
            'admin',
            'inspector',
            'owner'
        ])->select('id', 'name', 'display_name')->get();
        $sections = FormSection::where('form_version_id', $id)->select('id', 'name', 'sequence')->get();
        
        return view('form.setup', compact('form_version', 'option', 'roles', 'sections'));
    }

    public function destroy($id) {
        try {            
            if ($data = FormVersion::find($id)) {
                 
                $data->delete();
                               
                return [
                    'success-message' => 'Version already remove!'
                ];
            } else {                
                return [
                    'fail-message' => 'version not found!'
                ];
            }
        } catch (\Exception $e) {
            return [
                'fail-message' => 'fail to store record.'
            ];
        }
    }

    public function duplicate($id)
    {
        try {
            \DB::beginTransaction();
            $pending_exist = FormGroup::find($id)->versions()
                ->where('status', FormVersion::STATUS_PENDING)
                ->count();
            
            if ($pending_exist) {
                return [
                    'fail-message' => 'There is a version that still on pending status, please configure that version instead of create new one'
                ];
            }
            
            $form_version = FormGroup::find($id)->versions()
                ->latest()
                ->first();
            
            $new_version = $form_version->replicate();
            $new_version->version += 1;
            $new_version->status = FormVersion::STATUS_PENDING;
            $new_version->save();
            
            foreach ($form_version->sections as $s_key => $s_val) {
                $new_section = $s_val->replicate();
                $new_section->form_version_id = $new_version->id;
                $new_section->save();
            }
            
            foreach ($form_version->forms as $f_key => $f_val) {
                $new_form = $f_val->replicate();
                $new_form->form_version_id = $new_version->id;
                $new_form->save();
                
                foreach ($f_val->formAttributes as $fa_key => $fa_val) {
                    $new_fa = $fa_val->replicate();
                    $new_fa->form_id = $new_form->id;
                    $new_fa->form_section_id = $fa_val->section ? $new_version->sections()
                        ->where("name", $fa_val->section->name)
                        ->first()->id : null;
                    $new_fa->save();
                    
                    foreach ($fa_val->locations as $l_key => $l_val) {
                        $new_location = $l_val->replicate();
                        $new_location->form_attribute_id = $new_fa->id;
                        $new_location->save();
                    }              
                    
                    foreach ($fa_val->roles as $r_key => $l_val) {
                        $new_fa->roles()->attach($l_val);
                    }
                }
            }
            
            \DB::commit();
            
            return [
                'success-message' => 'New version created.'
            ];
        } catch (\Exception $e) {
            \DB::rollBack();
            return [
                'fail-message' => 'fail to store record.'
            ];
        }
    }

    public function publish($id)
    {
        try {
            \DB::beginTransaction();
            
            $form_version = FormVersion::find($id);
            
            $other_versions = FormVersion::where([
                'form_group_id' => $form_version->form_group_id,
                'status' => FormVersion::STATUS_ACTIVE
            ])->get();
            
            foreach ($other_versions as $key => $val) {
                $val->status = FormVersion::STATUS_INACTIVE;
                $val->save();
            }
            
            $form_version->status = FormVersion::STATUS_ACTIVE;
            $form_version->save();
            
            \DB::commit();
            
            return back()->with([
                'success-message' => 'Record successfully updated.'
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with([
                'fail-message' => 'Record fail to update.'
            ]);
        }
    }
}
