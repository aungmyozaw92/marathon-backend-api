<?php

namespace App\Http\Requests\Transaction;

use App\Http\Requests\FormRequest;
use App\Models\AccountInformation;

class CreateTopupRequest extends FormRequest
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
            'from_account_type' => 'required|string|in:Merchant',
            'to_account_type' => 'required|string|in:HQ,Branch,Agent',
            'amount' => 'required|numeric|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'from_account_id' => 'required|integer|exists:merchants,id',
            'to_account_id' => 'required|integer',
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
                if ($accountInformation->resourceable_type != request()->get('from_account_type') || $accountInformation->resourceable_id != request()->get('from_account_id')) {
                    $validator->errors()->add('account_information_id', 'Invalid Account Information');
                }
            }
        });
    }
}
