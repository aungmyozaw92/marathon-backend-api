<?php

namespace App\Http\Requests\FinanceExpense;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateFinanceExpenseRequest extends FormRequest
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
            // 'spend_at'      => 'required|string',
            'spend_on' => 'required|date_format:Y-m-d',
            'total' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'sub_total' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'branch_id' => 'nullable|integer|exists:branches,id',
            'staff_id' => 'nullable|integer|exists:staffs,id',
        ];
    }
}
