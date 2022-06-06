<?php

namespace App\Http\Requests\Zone;

use App\Http\Requests\FormRequest;

class UpdateZoneRequest extends FormRequest
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
            'name'              => 'required|string|unique:zones,name,' . $this->route('zone')->id,
            'name_mm'           => 'required|string|unique:zones,name_mm,' . $this->route('zone')->id,
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

    // public function updateZone($zone)
    // {
    //     $zone->name = $this->name;
    //     $zone->delivery_rate = $this->delivery_rate;
    //     $zone->city_id = $this->city_id;

    //     if($zone->isDirty()) {
    //         $zone->save();
    //     }
        
    //     return $zone;
    // }
}
