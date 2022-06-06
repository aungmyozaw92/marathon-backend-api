<?php

namespace App\Http\Requests\SuperMerchant\MerchantAssociate;

use App\Models\Merchant;
use App\Models\MerchantAssociate;

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
            'phones'           => 'required|array',
            'emails'           => 'nullable|array',
            'phones.*'         => 'required|string|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'emails.*'         => 'nullable|string',
            'label'            =>  'required|string',
            'address'          =>  'required|string',
            'city_id'          =>  'required|integer|exists:cities,id',
            'zone_id'          =>  'required|integer|exists:zones,id',
        ];
    }
}
