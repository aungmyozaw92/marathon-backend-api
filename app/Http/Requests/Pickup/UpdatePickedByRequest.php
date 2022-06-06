<?php

namespace App\Http\Requests\Pickup;

use App\Http\Requests\FormRequest;

class UpdatePickedByRequest extends FormRequest
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
            'pickup_id' => 'required|integer|exists:pickups,id',
            'pickuped_by' => 'required|integer|exists:staffs,id'
        ];
    }
}
