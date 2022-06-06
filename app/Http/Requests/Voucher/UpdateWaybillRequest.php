<?php

namespace App\Http\Requests\Voucher;

use App\Http\Requests\FormRequest;

class UpdateWaybillRequest extends FormRequest
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
            // 'delivery_status_id' => 'integer|in:8,9,10|exists:delivery_statuses,id'
            'delivery_status_id' => 'integer|in:2,8,9,10,'
        ];
    }
}
