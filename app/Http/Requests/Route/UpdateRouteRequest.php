<?php

namespace App\Http\Requests\Route;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateRouteRequest extends FormRequest
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
          // 'route_rate' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
           // 'route_agent_rate' => 'nullable|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'travel_day' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'origin_id' => 'required|integer|exists:cities,id|unique:routes,origin_id,' . $this->route('route')->id . ',id,destination_id,' . $this->destination_id,
            'destination_id' => 'required|integer|exists:cities,id',
        ];
    }
}
