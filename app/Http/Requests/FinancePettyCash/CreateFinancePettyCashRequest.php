<?php

namespace App\Http\Requests\FinancePettyCash;

use App\Http\Requests\FormRequest;

class CreateFinancePettyCashRequest extends FormRequest
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
            'spend_on' => 'required|date_format:Y-m-d',
            'total' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'branch_id' => 'nullable|integer|exists:branches,id',
            'staff_id' => 'nullable|integer|exists:staffs,id',
            'fn_paymant_option' => 'required|string',
            'petty_cash_items'   => 'required|array',
            'petty_cash_items.*.description' => 'nullable|string',
            'petty_cash_items.*.spend_at' => 'required|string',
            'petty_cash_items.*.amount' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            // 'petty_cash_items.*.amount' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'petty_cash_items.*.from_account_id' => 'required|integer|exists:finance_accounts,id',
            'petty_cash_items.*.to_account_id' => 'required|integer|exists:finance_accounts,id',
        ];
    }
}
