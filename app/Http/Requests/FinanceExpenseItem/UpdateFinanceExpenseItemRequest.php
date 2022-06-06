<?php

namespace App\Http\Requests\FinanceExpenseItem;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateFinanceExpenseItemRequest extends FormRequest
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
            'description'      => 'nullable|string',
            'qty' => 'required|integer',
            'amount' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'finance_account_id' => 'required|integer|exists:finance_accounts,id',
            'finance_expense_id' => 'required|integer|exists:finance_expenses,id',
        ];
    }
}



