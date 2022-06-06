<?php

namespace App\Http\Requests\CourierType;

use App\Http\Requests\FormRequest;

class UpdateCourierTypeRequest extends FormRequest
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
            'name'   => 'required|string|unique:courier_types,name,' . $this->route('courier_type')->id,
            'rate'   => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'cbm'    => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'weight' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/'
        ];
    }

    public function updateCourierType($courierType)
    {
        $courierType->name   = $this->name;
        $courierType->rate   = $this->rate;
        $courierType->cbm    = $this->cbm;
        $courierType->weight = $this->weight;

        if($courierType->isDirty()) {
            $courierType->updated_by = auth()->user()->id;
            $courierType->save();
        }
        
        return $courierType;
    }
}
