<?php

namespace App\Http\Requests\FinanceMasterType;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateFinanceMasterTypeRequest extends FormRequest
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
            'name'      => 'required|string|unique:finance_master_types,name,' . $this->route('finance_master_type')->id,
            'description' => 'nullable|string'
        ];
    }
}
