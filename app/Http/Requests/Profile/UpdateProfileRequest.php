<?php

namespace App\Http\Requests\Profile;

// use App\Models\Staff;
use App\Models\Staff;
use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdateProfileRequest extends FormRequest
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
            'name'               => 'nullable|string|max:255',
            'old_password'       => 'required_with:new_password|string|min:6',
            'new_password'       => 'nullable|confirmed|string|min:6',
            'phone'              => 'nullable|numeric',
        ];
    }

    /**
    * Configure the validator instance.
    *
    * @param  \Illuminate\Validation\Validator  $validator
    * @return void
    */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('old_password') && !Hash::check($this->old_password, \Auth::user()->password)) {
                $validator->errors()->add('old_password', 'Old password not valid');
            }
        });
    }

    public function updateProfile($staff) : Staff
    {
        $staff->name = $this->name ? $this->name : $staff->name;
        $staff->password = $this->new_password ? bcrypt($this->new_password) : $staff->password;
        $staff->phone = $this->phone ? $this->phone : $staff->phone;

        if ($staff->isDirty()) {
            $staff->updated_by = $staff->id;
            $staff->save();
        }

        return $staff->refresh();
    }
}
