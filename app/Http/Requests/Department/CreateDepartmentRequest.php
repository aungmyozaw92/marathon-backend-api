<?php

namespace App\Http\Requests\Department;

use App\Models\Department;
use App\Http\Requests\FormRequest;

class CreateDepartmentRequest extends FormRequest
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
            'authority' => 'required|string|unique:departments,authority',
            'department' => 'required|string|unique:departments,department'
        ];
    }

    // public function storeDepartment()
    // {
    //     return Department::create([
    //                 'authority' => $this->authority,
    //                 'department' => $this->department
    //             ]);
    // }
}
