<?php

namespace App\Http\Requests\Pickup;

use App\Http\Requests\FormRequest;

class UpdateRequestedDateRequest extends FormRequest
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
            'requested_date'          => 'required|date_format:Y-m-d|after_or_equal:' . date('m/d/Y'),
        ];
    }
}
