<?php

namespace App\Http\Requests\AgentBadge;

use App\Models\AgentBadge;
use App\Http\Requests\FormRequest;

class CreateAgentBadgeRequest extends FormRequest
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
            'name'                  => 'required|string|unique:agent_badges,name',
            'deposit'               => 'required|regex:/^\d{1,12}(\.\d{1,4})?$/',
            'logo'                  => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'monthly_reward'        => 'required|regex:/^\d{1,12}(\.\d{1,4})?$/',
            'delivery_points'       => 'required|integer',
            'weekly_payment'        => 'required|regex:/^\d{1,12}(\.\d{1,4})?$/',
            'monthly_good_credit'   => 'required|regex:/^\d{1,12}(\.\d{1,4})?$/'
        ];
    }
}
