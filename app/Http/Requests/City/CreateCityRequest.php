<?php

namespace App\Http\Requests\City;

use App\Models\City;
use App\Http\Requests\FormRequest;

class CreateCityRequest extends FormRequest
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
            'name' => 'required|string|unique:cities,name',
            // 'delivery_rate' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'is_collect_only' => 'nullable|boolean',
            'is_on_demand' => 'nullable|boolean',
            'is_available_d2d' => 'nullable|boolean',
            // 'is_active' => 'nullable|boolean',
            // 'locking' => 'nullable|boolean',
            // 'locked_by' => 'nullable|integer|exists:users,id'
        ];
    }

    // public function storeCity()
    // {
    //     return City::create([
    //                 'name' => $this->name,
    //                 'delivery_rate' => $this->delivery_rate,
    //                 'is_active' => $this->is_active ?: 0,
    //                 'locking' => $this->locking ?: 0,
    //                 'locked_by' => $this->locked_by,
    //             ]);
    // }
}
