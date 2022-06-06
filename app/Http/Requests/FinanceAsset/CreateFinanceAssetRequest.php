<?php

namespace App\Http\Requests\FinanceAsset;

use App\Http\Requests\FormRequest;

class CreateFinanceAssetRequest extends FormRequest
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
            'branch_id' => 'nullable|integer|exists:branches,id',
            'asset_type_id' => 'required|integer|exists:finance_asset_types,id',
            'accumulated_depreciation_account_id' => 'required|integer|exists:finance_accounts,id',
            'depreciation_expense_account_id' => 'required|integer|exists:finance_accounts,id',
            'description' => 'nullable|string',
            'serial_no' => 'nullable|string',
            'purchase_price' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'purchase_date' => 'required|date_format:Y-m-d',
            'depreciation_start_date' => 'required|date_format:Y-m-d',
            'warranty_month' => 'required|numeric',
            'depreciation_month' => 'required|numeric',
            'depreciation_rate' => 'required|string',
            
        ];
    }
}
