<?php

namespace App\Http\Requests\MerchantDashboard\Pickup;

use App\Http\Requests\FormRequest;

class UpdatePickupRequest extends FormRequest
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
            // 'merchant_associate_id' => 'required|integer|exists:merchant_associates,id',
            // 'note' => 'nullable|string',
            // 'is_called' => 'nullable|boolean',
            // 'requested_date' => 'required_if:is_called,true|date_format:Y-m-d|after_or_equal:' . date('m/d/Y')

            'merchant_associate_id' => 'required|integer|exists:merchant_associates,id',
            'note' => 'nullable|string',
            'opened_by' => 'nullable|integer|exists:staffs,id',
            'voucher_id' => 'nullable|array',
            'voucher_id.*' => 'nullable|integer|exists:vouchers,id'
        ];
    }
}
