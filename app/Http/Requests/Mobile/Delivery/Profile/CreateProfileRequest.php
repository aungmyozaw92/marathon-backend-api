<?php

namespace App\Http\Requests\Mobile\Delivery\Profile;

use App\Http\Requests\FormRequest;

class CreateProfileRequest extends FormRequest
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
            // '*.label'            => 'required|string',
            // '*.address'          => 'required|string',
            // '*.zone_id'      => 'nullable|integer|exists:zones,id',
            // '*.city_id'          => 'required|integer|exists:cities,id',
            // '*.phones.*.phone'   => 'required|phone:MM',
            // '*.emails.*.email'   => 'required|email',
        ];
    }
}
