<?php

namespace App\Http\Requests\Mobile\VariationMeta;

use App\Models\VariationMeta;
use App\Http\Requests\FormRequest;

class UpdateVariationMetaRequest extends FormRequest
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
            'key' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            // 'merchant_id'  => 'required|integer|exists:merchants,id'

        ];
    }

}
