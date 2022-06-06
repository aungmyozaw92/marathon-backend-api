<?php

namespace App\Http\Requests\FinancePosting;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateFinancePostingRequest extends FormRequest
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
            'description' => 'nullable|string',
            'status' => 'required|string',
            'branch_id' => 'required|integer|exists:branches,id,deleted_at,NULL',
            'from_finance_account_id' => 'required|integer|exists:finance_accounts,id,deleted_at,NULL',
            'to_finance_account_id' => 'required|integer|exists:finance_accounts,id,deleted_at,NULL',
        ];
    }
}
