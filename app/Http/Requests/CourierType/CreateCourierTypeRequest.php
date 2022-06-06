<?php

namespace App\Http\Requests\CourierType;

use App\Models\CourierType;
use App\Http\Requests\FormRequest;

class CreateCourierTypeRequest extends FormRequest
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
            'name'   => 'required|string|unique:courier_types,name',
            'rate'   => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'cbm'    => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/',
            'weight' => 'required|regex:/^\d{1,14}(\.\d{1,2})?$/'
        ];
    }

    public function storeCourierType()
    {
        return CourierType::create([
                    'name'   => $this->name,
                    'rate'   => $this->rate,
                    'cbm'    => $this->cbm,
                    'weight' => $this->weight,
                    'created_by' => auth()->user()->id
                ]);
    }
}
