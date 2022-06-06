<?php

namespace App\Http\Requests\Mobile\v2\Merchant;

use App\Http\Requests\FormRequest;
use App\Models\Merchant;
use App\Models\MerchantAssociate;

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
            'city_id'                     => 'required|integer|exists:cities,id',
            'zone_id'                     => 'required|integer|exists:zones,id',
            'address'                     => 'required|string',
            'phones'                      => 'required|array',
            'phones.*.phone'              => 'required|unique:contact_associates,value,NULL,id,deleted_at,NULL|phone:MM'
        ];
    }
    public function messages()
    {
        return [
            'phones.*.phone' => 'The phone field contains an invalid number'
        ];
    }
}
