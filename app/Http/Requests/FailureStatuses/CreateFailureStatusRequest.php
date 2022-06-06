<?php

namespace App\Http\Requests\FailureStatuses;

use App\Http\Requests\FormRequest;

class CreateFailureStatusRequest extends FormRequest
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
            'category'      => 'required|string|unique:failure_statuses,category',
            'specification' => 'required|string|unique:failure_statuses,specification'
        ];
    }
}
