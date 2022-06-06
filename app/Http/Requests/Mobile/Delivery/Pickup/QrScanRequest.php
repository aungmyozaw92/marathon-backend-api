<?php

namespace App\Http\Requests\Mobile\Delivery\Pickup;

use App\Http\Requests\FormRequest;

class QrScanRequest extends FormRequest
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
            'qr_code'   => 'required|string',
        ];
    }
}
