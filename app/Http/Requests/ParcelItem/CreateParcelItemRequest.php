<?php

namespace App\Http\Requests\ParcelItem;

use App\Http\Requests\FormRequest;

class CreateParcelItemRequest extends FormRequest
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
            // 'name' => 'required|string|unique:badges,name',
            // 'logo' => 'required|string',
            // 'description' => 'required|string',
        ];
    }
}
