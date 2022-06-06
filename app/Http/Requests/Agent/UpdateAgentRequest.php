<?php

namespace App\Http\Requests\Agent;

use Carbon\Carbon;
use App\Models\Agent;
use App\Http\Requests\FormRequest;

class UpdateAgentRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'account_code' => 'nullable|string|unique:agents,account_code,' . $this->route('agent')->id,
            'username' => 'required|string|unique:agents,username,' . $this->route('agent')->id,
            'password'         => 'nullable|string|min:6',
            'city_id' => 'required|integer|exists:cities,id',
            'agent_badge_id'  => 'nullable|integer|exists:agent_badges,id',
            // |unique:agents,city_id,' . $this->route('agent')->id . ',id,is_active,' . $this->is_active,
            'delivery_commission' => 'nullable|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'shop_name' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'is_active' => 'required|boolean'

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
            if ($this->has('agent_badge_id') && request()->get('agent_badge_id')) {
                if (($this->route('agent')->agent_badge_id != request()->get('agent_badge_id')) && !in_array(Carbon::now()->day, [1, 2, 3, 4, 5])) {
                    $validator->errors()->add('Agent Badge', 'You can only update Agent Badge in first 5 days of every month!');
                }
            }
        });
    }
}
