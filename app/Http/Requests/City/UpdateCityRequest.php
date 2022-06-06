<?php

namespace App\Http\Requests\City;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateCityRequest extends FormRequest
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
            'name' => 'required|string|unique:cities,name,' . $this->route('city')->id,
            // 'delivery_rate' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'is_collect_only' => 'nullable|boolean',
            'is_on_demand' => 'nullable|boolean',
            'is_available_d2d' => 'nullable|boolean',
            // 'is_active' => 'nullable|boolean',
            // 'is_active' => 'nullable|boolean',
            // 'locking' => 'nullable|boolean',
            // 'locked_by' => 'nullable|integer|exists:users,id'
        ];
    }

    // public function updateCity($city)
    // {
    //     $city->name = $this->name;
    //     $city->delivery_rate = $this->delivery_rate;
    //     $city->is_active = $this->is_active ?: $city->is_active;
    //     $city->locking = $this->locking ?: $city->locking;
    //     $city->locked_by = $this->locked_by;

    //     if($city->isDirty()) {
    //         $city->save();
    //     }
        
    //     return $city;
    // }
}
