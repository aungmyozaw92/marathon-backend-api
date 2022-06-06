<?php

namespace App\Http\Requests\FinanceMasterType;

use App\Http\Requests\FormRequest;

class CreateFinanceMasterTypeRequest extends FormRequest
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
            'name'      => 'required|string|unique:finance_master_types,name',
            'description' => 'nullable|string'
        ];
    }
}
