<?php

namespace App\Http\Requests\FinanceAccountType;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateFinanceAccountTypeRequest extends FormRequest
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
            'name'      => 'required|string|unique:finance_account_types,name,' . $this->route('finance_account_type')->id,
            'description' => 'nullable|string'
        ];
    }
}
