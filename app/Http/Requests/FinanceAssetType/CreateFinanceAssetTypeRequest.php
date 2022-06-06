<?php

namespace App\Http\Requests\FinanceAssetType;

use App\Http\Requests\FormRequest;

class CreateFinanceAssetTypeRequest extends FormRequest
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
            'name' => 'required|string',
            'accumulated_depreciation_account_id' => 'required|integer|exists:finance_natures,id',
            'depreciation_expense_account_id' => 'required|integer|exists:finance_natures,id',
            'depreciation_rate' => 'required|string',
            'branch_id' => 'nullable|integer|exists:branches,id',
        ];
    }
}
