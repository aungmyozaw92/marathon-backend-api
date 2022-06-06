<?php

namespace App\Http\Requests\Mobile\Store;

use App\Http\Requests\FormRequest;

class UpdateStoreRequest extends FormRequest
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
            'item_name'  => 'required|string',
            'item_price' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
        ];
    }
}
