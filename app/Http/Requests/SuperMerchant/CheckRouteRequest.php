<?php

namespace App\Http\Requests\SuperMerchant;

use App\Http\Requests\FormRequest;

class CheckRouteRequest extends FormRequest
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
            'receiver_city_id'                     => 'required|integer|exists:cities,id',
        ];
    }
}
