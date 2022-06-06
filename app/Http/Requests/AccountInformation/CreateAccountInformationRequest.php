<?php

namespace App\Http\Requests\AccountInformation;

use App\Http\Requests\FormRequest;

class CreateAccountInformationRequest extends FormRequest
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
            'account_name' => 'required|string|max:255',
            'account_no' => 'required|numeric',
            'resourceable_type' => 'required|in:MerchantAssociate,Agent',
            'sender_associate_id' => 'required_if:sender_type,MerchantAssociate|integer|exists:merchant_associates,id',
            'agent_id' => 'required_if:sender_type,Agent|integer|exists:agents,id',
            'bank_id' => 'required|integer|exists:banks,id'
        ];
    }
}
