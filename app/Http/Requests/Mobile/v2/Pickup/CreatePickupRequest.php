<?php

namespace App\Http\Requests\Mobile\v2\Pickup;

use App\Http\Requests\FormRequest;

class CreatePickupRequest extends FormRequest
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
        // dd($this->voucher_id);
        return [
            'requested_date'          => 'nullable|date_format:Y-m-d|after_or_equal:' . date('m/d/Y'),
            'merchant_associate_id' => 'required|integer|exists:merchant_associates,id',
            'note' => 'nullable|string',
            'opened_by' => 'nullable|integer|exists:staffs,id',
            'voucher_id' => 'nullable|array',
            'voucher_id.*' => 'integer|exists:vouchers,id'
        ];
    }
}
