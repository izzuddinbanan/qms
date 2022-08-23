<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entity\FormSection;

class FormSectionController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'form_version_id' => 'required',
            'name' => 'required',
            'sequence' => 'required'
        ];
        
        $this->validate($request, $rules);
        
        $form_section = FormSection::create([
            'name' => $request->input('name'),
            'sequence' => $request->input('sequence'),
            'form_version_id' => $request->input('form_version_id')
        ]);
        
        return redirect('form/version/' . $request->input('form_version_id'))->with('success-message', 'New record successfully added!');
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
            'name' => 'required',
            'sequence' => 'required'
        ];

        $this->validate($request, $rules);

        if ($data = FormSection::find($id)) {
            $data->name = $request->input('name');
            $data->sequence = $request->input('sequence');
            $data->save();
            
            return ['success-message' => 'Section update successful!'];
        } else {
            return ['fail-message' => 'Section not found!'];
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($data = FormSection::find($id)) {
            foreach($data->attirbuteInputs as $key => $val) {
                $val->update([
                    'form_section_id' => null
                ]);
            }
            
            $data->delete();
            
            return ['success-message' => 'Section already remove!'];
        } else {
            return ['fail-message' => 'Section not found!'];
        }
    }
}
