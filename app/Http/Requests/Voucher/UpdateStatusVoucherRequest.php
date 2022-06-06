<?php

namespace App\Http\Requests\Voucher;

use App\Http\Requests\FormRequest;

class UpdateStatusVoucherRequest extends FormRequest
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
            'receiver_id'             => 'required|integer|exists:customers,id',
            'receiver_name'           => 'required|string|max:255',
            //'receiver_phone'          => 'required|string|phone:MM|unique:customers,phone,' . $this->receiver_id,
            'receiver_phone'          => 'required|numeric|unique:customers,phone,' . $this->receiver_id,
            'receiver_address'        => 'nullable|string'
        ];
    }
}
