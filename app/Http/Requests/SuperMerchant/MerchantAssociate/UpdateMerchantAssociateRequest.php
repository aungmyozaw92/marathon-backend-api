<?php

namespace App\Http\Requests\SuperMerchant\MerchantAssociate;

use App\Models\Merchant;
use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdateMerchantAssociateRequest extends FormRequest
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
            'phones.*'           => 'nullable|array',
            'emails'           => 'nullable|array',
            'phones.*'         => 'nullable|string|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'emails.*'         => 'nullable|string',
            'label'            =>  'nullable|string',
            'address'          =>  'nullable|string',
            'city_id'          =>  'nullable|integer|exists:cities,id',
            'zone_id'          =>  'nullable|integer|exists:zones,id',
        ];
    }
}
