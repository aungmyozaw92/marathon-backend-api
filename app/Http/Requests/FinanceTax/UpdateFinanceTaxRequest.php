<?php

namespace App\Http\Requests\FinanceTax;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateFinanceTaxRequest extends FormRequest
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
            'name'      => 'required|string|unique:finance_taxes,name,' . $this->route('finance_tax')->id,
            'description' => 'nullable|string',
            'amount' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/'
        ];
    }
}
