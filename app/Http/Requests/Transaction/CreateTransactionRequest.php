<?php

namespace App\Http\Requests\Transaction;

use App\Http\Requests\FormRequest;

class CreateTransactionRequest extends FormRequest
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
            'type' => 'nullable|in:Topup,Withdraw',
            'amount' => 'required|numeric|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'extra_amount' => 'nullable|numeric|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'city_id' => 'required|integer|exists:cities,id',
            'merchant_id' => 'nullable|integer|exists:merchants,id',
            'account_information_id' => 'nullable|integer|exists:account_informations,id'
        ];
    }
}
