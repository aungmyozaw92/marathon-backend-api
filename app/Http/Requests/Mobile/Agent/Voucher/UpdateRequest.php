<?php

namespace App\Http\Requests\Mobile\Agent\Voucher;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
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
            'delivery_status_id'        => 'required|integer|exists:delivery_statuses,id',
            'waybill_id'=>'required|integer',
            // this depend on characters of failure_statuses
            // 'note' => 'bail|required_if:delivery_status_id,==,10|nullable|regex:/^[\p{L},\p{Myanmar},\p{N},.!? ""]+(,[\p{L},\p{Myanmar},\p{N},.!? ""]+){0}$/u',
            'notes' => 'bail|required_if:delivery_status_id,==,10|nullable|min:2',
        ];
    }
}
