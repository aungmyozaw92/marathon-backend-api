<?php

namespace App\Http\Requests\Mobile\Agent\Waybill;

use App\Http\Requests\FormRequest;

class ReceivedRequest extends FormRequest
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
            'is_received'        => 'required|in:0,1'
        ];
    }
}
