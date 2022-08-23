<?php
namespace App\Http\Controllers;

use App\Entity\FormVersion;
use Illuminate\Http\Request;
use App\Entity\FormAttribute;
use App\Entity\FormAttributeLocation;
use App\Entity\Form;
use PDF;
use Dompdf\Adapter\CPDF;
use Dompdf\Dompdf;
use Carbon\Carbon;

class FormAttributeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('isSuperUser');
    }

    public function saveAll(Request $request)
    {
        $rules = [
            'version_id' => 'required|exists:form_versions,id',
            'forms' => 'required|array',
            'forms.*.id' => 'required|exists:forms,id',
            'forms.*.attributes' => 'nullable|array',
            'forms.*.attributes.*.id' => 'nullable|exists:form_attributes,id',
            'forms.*.attributes.*.attribute_id' => 'required|exists:attributes,id',
            'forms.*.attributes.*.is_required' => 'required|in:0,1',
            'forms.*.attributes.*.form_section_id' => 'nullable|exists:form_sections,id,form_version_id,' . $request->input('version_id'),
            'forms.*.attributes.*.key' => 'required|string',
            
            'forms.*.attributes.*.locations' => 'required|array',
            'forms.*.attributes.*.locations.*.id' => 'nullable|exists:form_attribute_locations,id',
            'forms.*.attributes.*.locations.*.x' => 'required',
            'forms.*.attributes.*.locations.*.y' => 'required',
            'forms.*.attributes.*.locations.*.width' => 'required',
            'forms.*.attributes.*.locations.*.height' => 'required',
            'forms.*.attributes.*.locations.*.value' => 'nullable',
            'forms.*.attributes.*.locations.*.number_of_row' => 'nullable|integer',
            
            'forms.*.attributes.*.roles' => 'array',
            'forms.*.attributes.*.roles.*' => 'nullable|exists:roles,id'
        ];
        
        $this->validate($request, $rules);
        
        try {
            \DB::beginTransaction();
            $form_version = FormVersion::find($request->input('version_id'));
            
            $forms = $request->input('forms');

            foreach($forms as $key => $form) {
                if (isset($form['attributes'])) {
                    $id_arr = (collect($form['attributes']))->pluck('id');
                    Form::find($form['id'])->formAttributes()->whereNotIn('id', $id_arr)->delete();

                    foreach ($form["attributes"] as $a_key => $a_attribute) {
                        $form_attribute = $a_attribute["id"] == null ? new FormAttribute() : FormAttribute::find($a_attribute["id"]);
                        $form_attribute->form_id = $form["id"];
                        $form_attribute->key = $a_attribute['key'];
                        $form_attribute->attribute_id = $a_attribute['attribute_id'];
                        $form_attribute->is_required = $a_attribute['is_required'];
                        $form_attribute->form_section_id = $a_attribute['form_section_id'] ? $a_attribute['form_section_id'] : null;
                        $form_attribute->save();
                        
                        $location_id_arr = (collect($a_attribute['locations']))->pluck('id');
                        $form_attribute->locations()->whereNotIn('id', $location_id_arr)->delete();
                        foreach ($a_attribute["locations"] as $l_key => $l_location) {
                            $attribute_location = $l_location["id"] == null ? new FormAttributeLocation() : FormAttributeLocation::find($l_location["id"]);
                            $attribute_location->position_x = $l_location['x'];
                            $attribute_location->position_y = $l_location['y'];
                            $attribute_location->width = $l_location['width'];
                            $attribute_location->height = $l_location['height'];
                            $attribute_location->value = $l_location['value'] ? $l_location['value'] : null;
                            $attribute_location->number_of_row = $l_location['number_of_row'] ? $l_location['number_of_row'] : 0;
                            $attribute_location->form_attribute_id = $form_attribute->id;
                            $attribute_location->save();                        
                        }
                        
                        $form_attribute->roles()->detach();
                        if (isset($a_attribute['roles'])) {
                            foreach ($a_attribute['roles'] as $role) {
                                $form_attribute->roles()->attach($role);
                            }
                        }
                    }
                } else {
                    Form::find($form['id'])->formAttributes()->delete();
                }

            }
            
            \DB::commit();
            return ['success-message' => 'Setup store successful'];
        } catch (\Exception $e) {
            \DB::rollback();
            return ['fail-message' => 'Fail to store setting'];
        }
    }

    public function printPDF(Request $request) {
        
        $file_size = CPDF::$PAPER_SIZES['a4'];
    
        $inputs = $request->all();

        $html = '<html><head></head><body>';
        foreach ($inputs['input'] as $form) {
            $content = '<div style="width: 100%; position: relative;"><img style="width: 700px; height: 990px" src="' . $form['file'] . '"/>';

            if(isset($form['attributes'])){
                    
                foreach ($form['attributes'] as $attribute) {
                    foreach ($attribute['locations'] as $location) {

                        $width = $location["width"] * 700 / $form["width"];
                        $height = $location["height"] * 990 / $form["height"];
                        $position_left = $location["x"] * 700 / $form["width"];
                        $position_top = $location["y"] * 990 / $form["height"];
                        
                        switch ($attribute["attribute_id"]) {
                            case 1:
                            case 2:
                            case 5:
                            case 9:
                                $content .= '<div style="position: absolute; height:'. $height .'px; width:' . $width .'px; top:'. $position_top .'px; left:' . $position_left .'px">' . $location['preview_input'] . '</div>';
                                break;
                            case 3:
                                $content .= '<div style="position: absolute; height:'. $height .'px; width:' . $width .'px; top:'. $position_top .'px; left:' . $position_left .'px">' . '<img src="' . $location["preview_input"] . '" style="height:' . $height . 'px;"/>' . '</div>';
                                break;
                            case 4:
                                break;
                            case 6:
                            case 7:
                            case 8:
                                if ($location["preview_input"] == "1") {
                                    $content .= '<div style="vertical-align: middle;" align="center; position: absolute; height:'. $height .'px; width:' . $width .'px; top:'. $position_top .'px; left:' . $position_left .'px"> V </div>';
                                }
                                break;
                        }
                    }
                }
            }
            
            $html .= $content . '</div>';  
        } 

        $html .= '</body></html>';        
        
        $filename = Carbon::now()->format('dmy') . 'preview.pdf';
        PDF::loadHTML($html)->setPaper('a4', 'portrait')->setWarnings(false)->save($filename);

        return asset($filename);
    }
}
