<?php

namespace App\Http\Requests\BranchSheet;

use App\Http\Requests\FormRequest;

class CreateBranchSheetRequest extends FormRequest
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
            'branch_id' => 'required|integer|exists:branches,id',
            'voucher_id' => 'required|array',
            'voucher_id.*' => 'integer|exists:vouchers,id',
            'qty' => 'required|integer',
        ];
    }
}
