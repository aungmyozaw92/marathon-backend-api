<?php

namespace App\Http\Requests\VoucherMessage;

use App\Http\Requests\FormRequest;

class UpdateVoucherMessageRequest extends FormRequest
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
            // 'staff_id'     => 'required|integer|exists:staffs,id',
            'message_text' => 'required|string',
        ];
    }
}
