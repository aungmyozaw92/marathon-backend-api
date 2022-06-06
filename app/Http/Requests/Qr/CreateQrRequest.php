<?php

namespace App\Http\Requests\Qr;

use App\Http\Requests\FormRequest;

class CreateQrRequest extends FormRequest
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
        return  [
            'actor_type' => 'required|in:Merchant,Customer,Agent',
            'merchant_id' => 'required_if:actor_type,Merchant|integer|exists:merchants,id',
            'customer_id' => 'required_if:actor_type,Customer|integer|exists:customers,id',
            'agent_id' => 'required_if:actor_type,Agent|integer|exists:agents,id',
            'qty' => 'required|integer',
        ];
    }
}
