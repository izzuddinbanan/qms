<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComposeEmailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'subject'      => 'required',
            'content'      => 'required',
            'recipient_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'subject.required'      => 'Subject field are required.',
            'content.required'      => 'Content field are required.',
            'recipient_id.required' => 'Please select one recipient.',
        ];
    }
}
