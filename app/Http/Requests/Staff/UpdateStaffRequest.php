<?php

namespace App\Http\Requests\Staff;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateStaffRequest extends FormRequest
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
            'name'             => 'required|string|max:255',
            'username'         => 'required|string|unique:staffs,username,' . $this->route('staff')->id,
            // 'phone'            => 'nullable|numeric|unique:staffs,phone,' . $this->route('staff')->id,
            'phone'            => 'nullable|numeric',
            'password'         => 'nullable|string|min:6',
            'role_id'          => 'required|integer|exists:roles,id',
            'department_id'    => 'required|integer|exists:departments,id',
            'city_id'          => 'required|integer|exists:cities,id',
            'staff_type'        => 'nullable|string|in:In-house,Freelance,"Freelance Car"',
            'is_commissionable' => 'nullable|boolean',
            'is_pointable'      => 'nullable|boolean',
            'zone_id'          => 'nullable|integer|exists:zones,id|required_if:department_id,5|required_if:role_id,5',
            'courier_type_id'  => 'nullable|integer|exists:courier_types,id|required_if:department_id,5|required_if:role_id,5',
        ];
    }
}
