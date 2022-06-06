<?php

namespace App\Http\Requests\FinanceAdvance;

use App\Http\Requests\FormRequest;

class CreateFinanceAdvanceRequest extends FormRequest
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
            'amount'      => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'reason' => 'nullable|string',
            'branch_id' => 'required|integer|exists:branches,id',
            'staff_id' => 'nullable|integer|exists:staffs,id',
            'from_finance_account_id' => 'required|integer|exists:finance_accounts,id',
            'to_finance_account_id' => 'required|integer|exists:finance_accounts,id',
            'status' => 'nullable|in:0,1',
        ];
    }
}