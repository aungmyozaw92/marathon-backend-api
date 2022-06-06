<?php

namespace App\Http\Requests\Deduction;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateDeductionRequest extends FormRequest
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
            'points'        => 'required|regex:/^\d{1,12}(\.\d{1,4})?$/',
            'description'   => 'required|string|unique:deductions,description,' . $this->route('deduction')->id,
        ];
    }
}
