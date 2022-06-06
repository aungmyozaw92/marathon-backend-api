<?php

namespace App\Http\Requests\Department;

use App\Http\Requests\FormRequest;

class UpdateDepartmentRequest extends FormRequest
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
            'authority' => 'required|string|unique:departments,authority,' . $this->route('department')->id,
            'department' => 'required|string|unique:departments,department,' . $this->route('department')->id
        ];
    }

    // public function updateDepartment($department)
    // {
    //     $department->authority  = $this->authority;
    //     $department->department = $this->department;

    //     if($department->isDirty()) {
    //         $department->save();
    //     }
        
    //     return $department;
    // }
}
