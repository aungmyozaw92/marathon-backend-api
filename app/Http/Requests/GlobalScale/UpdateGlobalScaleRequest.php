<?php

namespace App\Http\Requests\GlobalScale;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateGlobalScaleRequest extends FormRequest
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
            'cbm' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'support_weight' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'max_weight' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            //'global_scale_rate' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            //'salt' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'description' => 'required|string',
            'description_mm' => 'nullable|string'
        ];
    }
}
