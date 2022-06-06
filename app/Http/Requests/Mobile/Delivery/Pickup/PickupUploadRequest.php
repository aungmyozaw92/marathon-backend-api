<?php

namespace App\Http\Requests\Mobile\Delivery\Pickup;

use App\Http\Requests\FormRequest;

class PickupUploadRequest extends FormRequest
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
            'note'   => 'nullable|string',
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ];
    }
}
