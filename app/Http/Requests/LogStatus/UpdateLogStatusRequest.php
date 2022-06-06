<?php

namespace App\Http\Requests\LogStatus;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLogStatusRequest extends FormRequest
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
            'value' => 'required|string|unique:log_statuses,value'. $this->route('log_status')->id,
            'description' => 'required|string|unique:log_statuses,description'. $this->route('log_status')->id,
            'description_mm' => 'nullable|required|string|unique:log_statuses,description'
        ];
    }
}
