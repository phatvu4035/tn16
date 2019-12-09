<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditEmpRentRequest extends FormRequest
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
            'emp_name' => 'required',
            'emp_live_status' => 'required',
            'identity_type' => 'required',
            'identity_code' => 'required|unique:employee_rent,identity_code,'.$this->get('id'),
        ];
    }
}
