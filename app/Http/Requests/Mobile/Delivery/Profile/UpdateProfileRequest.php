<?php

namespace App\Http\Requests\Mobile\Delivery\Profile;

// use App\Models\Staff;
use Illuminate\Validation\Rule;
use App\Http\Requests\FormRequest;
use App\Models\Delivery;
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

    public function updateProfile($staff) : Delivery
    {
        $staff->name = $this->name ? $this->name : $staff->name;
        $staff->password = $this->new_password ? bcrypt($this->new_password) : $staff->password;

        if ($staff->isDirty()) {
            $staff->updated_by = $staff->id;
            $staff->save();
        }

        return $staff->refresh();
    }
}
