<?php

namespace App\Http\Requests\Account;

use App\Http\Requests\FormRequest;

class UpdateBalanceRequest extends FormRequest
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
            'amount' => 'required|numeric|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'account_type' => 'required|in:Merchant,Agent,Branch',
            'merchant_id' => 'required_if:account_type,Merchant|integer|exists:merchants,id',
            'agent_id' => 'required_if:account_type,Agent|integer|exists:agents,id',
            'branch' => 'required_if:account_type,Branch|integer|exists:branches,id',
        ];
    }
}
