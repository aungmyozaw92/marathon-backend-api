<?php

namespace App\Http\Requests\Mobile\v2\MerchantAssociate;

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
            'label'         => 'required|string',
            'address'       => 'required|string',
            'zone_id'       => 'nullable|integer|exists:zones,id',
            'city_id'       => 'required|integer|exists:cities,id',
            'phone'         => 'required|phone:MM',
        ];
    }
    public function messages()
    {
        return [
            'phone.phone' => 'The phone field contains an invalid number'
        ];
    }
}
