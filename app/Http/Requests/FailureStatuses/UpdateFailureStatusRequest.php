<?php

namespace App\Http\Requests\FailureStatuses;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateFailureStatusRequest extends FormRequest
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
            'category'      => 'required|string|unique:failure_statuses,category,' . $this->route('failure_status')->id,
            'specification' => 'required|string|unique:failure_statuses,specification,' . $this->route('failure_status')->id
        ];
    }
}
