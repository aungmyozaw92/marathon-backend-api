<?php

namespace App\Http\Requests\MerchantAssociate;

use App\Http\Requests\FormRequest;

class CreateMerchantAssociateRequest extends FormRequest
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
            'merchant_id'        => 'required|integer|exists:merchants,id',
            'label'              => 'required|string',
            'address'            => 'required|string',
            'zone_id'            => 'nullable|integer|exists:zones,id',
            'city_id'            => 'required|integer|exists:cities,id',
            'phones'             => 'required|array',
            'phones.*.phone'     => 'required|numeric',
            'emails'             => 'nullable|array',
            'emails.*.email'     => 'nullable|email',
        ];
    }
}
