<?php

namespace App\Http\Requests\Tag;

use App\Models\Tag;
use App\Http\Requests\FormRequest;

class UpdateTagRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'merchant_id'  => 'required|integer|exists:merchants,id'

        ];
    }

}