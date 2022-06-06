<?php

namespace App\Http\Requests\FinanceAccount;

use App\Http\Requests\FormRequest;

class CreateFinanceAccountRequest extends FormRequest
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
            'name'      => 'required|string',
            'code'      => 'required|string|unique:finance_accounts,code',
            'description' => 'nullable|string',
            'finance_nature_id' => 'required|integer|exists:finance_natures,id',
            'finance_master_type_id' => 'required|integer|exists:finance_master_types,id',
            'finance_account_type_id' => 'required|integer|exists:finance_account_types,id',
            'finance_group_id' => 'required|integer|exists:finance_groups,id',
            'branch_id' => 'nullable|integer|exists:branches,id',
            'finance_tax_id' => 'required|integer|exists:finance_taxes,id',
            'finance_code_id' => 'required|integer|exists:finance_codes,id',
        ];
    }
}
