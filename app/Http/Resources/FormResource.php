<?php
namespace App\Http\Resources;

use App\Entity\SubmissionFormGroup;
use App\Entity\Submission;

class FormResource extends BaseResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $submissions = Submission::where('location_id', $this->location_id)->where('form_group_id', $this->id)->latest()->first();
        
        $submissionHistory = Submission::where('location_id', $this->location_id)->where('form_group_id', $this->id)->latest()->get();

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at->toDateTimeString(),
            'form_history' => new FormSubmissionColllection($submissionHistory),
            'link_issue' => new IssueCollection($this->link_issue),
        ];


        $latest = $this->latestVersion()->first();
        
        $data['forms'] = [];
        foreach ($latest->forms as $f_key => $f_val) {
            $form_obj = [];
            $form_obj['url'] = $f_val->file_url;
            $form_obj['width'] = $f_val->width;
            $form_obj['height'] = $f_val->height;

            $form_obj['version_attributes'] = [];
            foreach ($f_val->formAttributes as $key => $val) {
                $fa_obj = [];
                $fa_obj['key'] = $val['key'];
                $fa_obj['is_required'] = $val['is_required'];
                
                $attribute = $val['attribute'];
                
                $fa_obj['locations'] = [];
                foreach ($val['locations'] as $location) {
                    $location_obj = [];
                    $location_obj['location_id'] = $location['id'];
                    $location_obj['position_x'] = $location['position_x'];
                    $location_obj['position_y'] = $location['position_y'];
                    $location_obj['width'] = $location['width'];
                    $location_obj['height'] = $location['height'];
                    $location_obj['id_type'] = $attribute['id'];
                    $location_obj['attribute_name'] = $attribute['display_name'];
                    $location_obj['placeholder'] = $attribute['id'] == 1 || $attribute['id'] == 2 ? 'Enter text here' : '';
                    $location_obj['background_color'] = "#ffefd5";
                    $location_obj['number_of_row'] = $location['number_of_row'];
                    $location_obj['value_dropdown'] = $location['value'] ? $location['value'] : null;
                    $location_obj['value'] = null;
                    
                    if($submissions){

                        $oldest_data = SubmissionFormGroup::where('submission_id', $submissions->id)->where('form_attribute_location_id', $location['id'])->select('value as input_value')->first();

                        $location_obj['input_value'] = $oldest_data["input_value"];
                        
                    }else{

                        $location_obj['input_value'] = null;

                    }
                    $fa_obj['locations'][] = convert_null_to_string($location_obj);


                }
                
                $form_obj['version_attributes'][] = convert_null_to_string($fa_obj);
            }

            $data['status'] = (new FormGroupStatusCollection($this->formStatus));
            
            $data['forms'][] = convert_null_to_string($form_obj);
        }

        return convert_null_to_string($data);
    }
}
