<?php

namespace App\Http\Requests\BusDropOff;

use App\Models\BusDropOff;
use App\Http\Requests\FormRequest;

class CreateBusDropOffRequest extends FormRequest
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
            'route_id' => 'required|integer|exists:routes,id',
            'global_scale_id' => 'required|integer|exists:global_scales,id',
            'base_rate' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'agent_base_rate' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'salt' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
          //  'agent_salt' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
        ];
    }
}
