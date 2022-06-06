<?php

namespace App\Http\Requests\Voucher;

use App\Http\Requests\FormRequest;

class ManualCloseVoucherRequest extends FormRequest
{
    protected $casts = ['bus_station' => 'boolean'];
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
        // To do multiple validation rule for bus station and normal
        return [
             'vouchers' => 'required|array',
             'vouchers.*id' => 'nullable|integer|exists:vouchers,id',
        ];
    }
}
