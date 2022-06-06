<?php

namespace App\Http\Requests\Mobile\Gate;

//use App\Models\Gate;
use App\Http\Requests\FormRequest;

class GateByCityRequest extends FormRequest
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
            'from_city_id' => 'required|integer|exists:cities,id',
            'to_city_id' => 'required|integer|exists:cities,id',
        ];
    }
}
