<?php

namespace App\Http\Requests\Flag;

use App\Http\Requests\FormRequest;

class UpdateFlagRequest extends FormRequest
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
            'name' => 'required|string|unique:flags,name,' . $this->route('flag')->id,
            'logo' => 'nullable|string',
            'description' => 'required|string',
        ];
    }
}
