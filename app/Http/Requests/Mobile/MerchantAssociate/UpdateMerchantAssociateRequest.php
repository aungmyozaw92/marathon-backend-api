<?php

namespace App\Http\Requests\Mobile\MerchantAssociate;

use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;

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
            'label'                    => 'required|string',
            'address'                  => 'required|string',
            'zone_id'              => 'nullable|integer|exists:zones,id',
            'city_id'                  => 'required|integer|exists:cities,id',
            'phones.*.phone'           => 'required|phone:MM',
            'emails.*.email'           => 'required|email',
            'phones.*.phone.id'        => 'nullable|integer|exists:contact_associates,id',
            'emails.*.email.id'        => 'nullable|integer|exists:contact_associates,id',
            'phones.*.phone.is_delete' => 'nullable|boolean',
            'emails.*.email.is_delete' => 'nullable|boolean',
        ];
    }
}
