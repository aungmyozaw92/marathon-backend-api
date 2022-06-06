<?php

namespace App\Http\Requests\FinanceTableOfAuthority;

use App\Http\Requests\FormRequest;

class CreateFinanceTableOfAuthorityRequest extends FormRequest
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
            'petty_amount' => 'required|numeric|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'expense_amount' => 'nullable|numeric|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'advance_amount' => 'nullable|numeric|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'staff_id' =>  'nullable|integer|exists:staffs,id',
            'manager_id' => 'nullable|integer|exists:staffs,id',
            'is_need_approve' => 'nullable'
        ];
    }
}
