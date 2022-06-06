<?php

namespace App\Http\Requests\FinanceExpense;

use App\Http\Requests\FormRequest;

class CreateFinanceExpenseRequest extends FormRequest
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
            // 'spend_at'      => 'required|string',
            'spend_on' => 'required|date_format:Y-m-d',
            'total' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            // 'sub_total' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'branch_id' => 'nullable|integer|exists:branches,id',
            'staff_id' => 'nullable|integer|exists:staffs,id',
            // 'description' => 'required|string',
            'expense_items'   => 'required|array',
            'expense_items.*.description' => 'nullable|string',
            // 'expense_items.*.qty' => 'nullable|numeric',
            'expense_items.*.spend_at' => 'required|string',
            'expense_items.*.amount' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'expense_items.*.from_account_id' => 'required|integer|exists:finance_accounts,id',
            'expense_items.*.to_account_id' => 'required|integer|exists:finance_accounts,id',
        ];
    }
}
