<?php

namespace App\Http\Requests\FinanceConfig;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateFinanceConfigRequest extends FormRequest
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
            'screen'      => 'required|string',
            'from_finance_account_id' => 'required|integer|exists:finance_accounts,id',
            'to_finance_account_id' => 'nullable|integer|exists:finance_accounts,id',
            'branch_id' => 'required|integer|exists:branches,id',
        ];
    }
}
