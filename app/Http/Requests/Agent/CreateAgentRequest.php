<?php

namespace App\Http\Requests\Agent;

use App\Http\Requests\FormRequest;

class CreateAgentRequest extends FormRequest
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
            'account_code' => 'nullable|string|unique:agents,account_code',
            'username' => 'required|string|unique:agents,username',
            'shop_name' => 'nullable|string',
            'password' => 'required|string|min:6',
            'city_id'  => 'required|integer|exists:cities,id',
            'agent_badge_id'  => 'required|integer|exists:agent_badges,id',
            // unique:agents,city_id,NULL,id,is_active,' . $this->is_active
            'delivery_commission' => 'nullable|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'is_active' => 'required|boolean'
        ];
    }
}
