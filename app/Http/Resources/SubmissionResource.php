<?php
namespace App\Http\Resources;

use App\Entity\Form;

class SubmissionResource extends BaseResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'reference_no' => $this->reference_no,
            'created_at' => $this->created_at->toDateTimeString()
        ];
        
        $data['forms'] = [];
        foreach ($this->inputs as $key => $val) {
            $form = [];
            
            $form['inputs'] = [];
            foreach ($val as $input_key => $input_val) {
                $form['form_id'] = $input_val->form_id;
                $form['form_height'] = $input_val->form_height;
                $form['form_width'] = $input_val->form_width;
                $form['form_file'] = asset(Form::FILE_PATH . '/' . $input_val->form_file);
                
                $form['inputs'][] = convert_null_to_string([
                    'input_id_type' => $input_val->input_id_type,
                    'input_key' => $input_val->input_key,
                    'input_value' => $input_val->input_value,
                    'input_position_x' => $input_val->input_position_x,
                    'input_position_y' => $input_val->input_position_y,
                    'input_height' => $input_val->input_height,
                    'input_width' => $input_val->input_width
                ]);
            }
            
            $data['forms'][] = convert_null_to_string($form);
        }
        
        return convert_null_to_string($data);
    }
}
