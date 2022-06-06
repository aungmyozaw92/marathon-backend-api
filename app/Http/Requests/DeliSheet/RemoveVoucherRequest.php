<?php

namespace App\Http\Requests\DeliSheet;

use App\Http\Requests\FormRequest;

class RemoveVoucherRequest extends FormRequest
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
            'vouchers' => 'required|array',
            'vouchers.*.id' => 'required|integer|exists:vouchers,id',    
        ];
    }
}
