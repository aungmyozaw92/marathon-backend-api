<?php

namespace App\Http\Requests\Zone;

use App\Models\Zone;
use App\Http\Requests\FormRequest;

class CreateZoneRequest extends FormRequest
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
            'name'              => 'required|string|unique:zones,name',
            'name_mm'           => 'required|string|unique:zones,name_mm',
            'zone_rate'         => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'diff_zone_rate'    => 'nullable|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'zone_agent_rate'   => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'zone_commission'   => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'outsource_rate'    => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'outsource_car_rate'=> 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            // 'delivery_rate'  => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'city_id'           => 'required|integer|exists:cities,id',
            'note'              => 'nullable|string',
            'is_deliver'        => 'required|boolean'
        ];
    }

    // public function storeZone()
    // {
    //     return Zone::create([
    //                 'name' => $this->name,
    //                 'delivery_rate' => $this->delivery_rate,
    //                 'city_id' => $this->city_id
    //             ]);
    // }
}
