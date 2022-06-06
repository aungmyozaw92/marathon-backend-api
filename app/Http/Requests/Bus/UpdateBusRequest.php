<?php

namespace App\Http\Requests\Bus;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateBusRequest extends FormRequest
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
            'name' => 'required|string|unique:buses,name,' . $this->route('bus')->id
        ];
    }
}
