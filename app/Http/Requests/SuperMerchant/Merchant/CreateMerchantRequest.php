<?php

namespace App\Http\Requests\SuperMerchant\Merchant;

use App\Models\Merchant;
use App\Models\MerchantAssociate;

use App\Http\Requests\FormRequest;

class CreateMerchantRequest extends FormRequest
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
            'name'                        => 'required|string|max:255',
            'username'                    => 'required|string|unique:merchants,username',
            'password'                    => 'required|string|min:6',
            'merchant_associates'                    => 'required|array',
            'merchant_associates.*.phones'           => 'required|array',
            'merchant_associates.*.emails'           => 'nullable|array',
            'merchant_associates.*.phones.*'         => 'required|string|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'merchant_associates.*.emails.*'         => 'nullable|string',
            'merchant_associates.*.label'            =>  'required|string',
            'merchant_associates.*.address'          =>  'required|string',
            'merchant_associates.*.city_id'          =>  'required|integer|exists:cities,id',
            'merchant_associates.*.zone_id'          =>  'required|integer|exists:zones,id',
            'merchant_associates.*.account_no'       =>  'nullable|string',
            'merchant_associates.*.account_name'     =>  'nullable|string',
            'account_informations'        => 'nullable|array',
            'account_informations.*.bank_id'       =>  'required|integer|exists:banks,id',
            'account_informations.*.account_no'       =>  'required|string',
            'account_informations.*.account_name'     =>  'required|string',
        ];
    }
}
