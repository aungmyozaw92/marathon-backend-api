<?php

namespace App\Http\Requests\Transaction;

use App\Http\Requests\FormRequest;
use App\Models\AccountInformation;

class CreateWithdrawRequest extends FormRequest
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
            'note' => 'nullable|string',
            'from_account_type' => 'required|string|in:HQ,Branch,Agent',
            'to_account_type' => 'required|string|in:Merchant',
            'amount' => 'required|numeric|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'to_account_id' => 'required|integer|exists:merchants,id',
            'from_account_id' => 'required|integer',
            'account_information_id' => 'nullable|integer|exists:account_informations,id'
        ];
    }

    /**
    * Configure the validator instance.
    *
    * @param  \Illuminate\Validation\Validator  $validator
    * @return void
    */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('account_information_id') && request()->get('account_information_id')) {
                $accountInformation = AccountInformation::findOrFail(request()->get('account_information_id'));
                if ($accountInformation->resourceable_type != request()->get('to_account_type') || $accountInformation->resourceable_id != request()->get('to_account_id')) {
                    $validator->errors()->add('account_information_id', 'Invalid Account Information');
                }
            }
        });
    }
}
