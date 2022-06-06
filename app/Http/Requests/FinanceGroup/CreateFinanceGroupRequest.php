<?php

namespace App\Http\Requests\FinanceGroup;

use App\Http\Requests\FormRequest;

class CreateFinanceGroupRequest extends FormRequest
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
            'name'      => 'required|string|unique:finance_groups,name',
            'description' => 'nullable|string'
        ];
    }
}
