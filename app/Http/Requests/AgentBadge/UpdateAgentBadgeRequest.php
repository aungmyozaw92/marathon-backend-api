<?php

namespace App\Http\Requests\AgentBadge;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateAgentBadgeRequest extends FormRequest
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
            'name'                  => 'nullable|string|unique:agent_badges,name,' . $this->route('agent_badge')->id,
            'deposit'               => 'nullable|regex:/^\d{1,12}(\.\d{1,4})?$/',
            'logo'                  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'monthly_reward'        => 'nullable|regex:/^\d{1,12}(\.\d{1,4})?$/',
            'delivery_points'       => 'nullable|integer',
            'weekly_payment'        => 'nullable|regex:/^\d{1,12}(\.\d{1,4})?$/',
            'monthly_good_credit'   => 'nullable|regex:/^\d{1,12}(\.\d{1,4})?$/'
        ];
    }
}
