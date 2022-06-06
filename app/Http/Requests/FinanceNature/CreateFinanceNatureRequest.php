<?php

namespace App\Http\Requests\FinanceNature;

use App\Http\Requests\FormRequest;

class CreateFinanceNatureRequest extends FormRequest
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
            'name'      => 'required|string|unique:finance_natures,name',
            'description' => 'nullable|string'
        ];
    }
}
