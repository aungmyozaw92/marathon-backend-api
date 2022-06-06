<?php

namespace App\Http\Requests\Branch;

use App\Models\Branch;
use App\Http\Requests\FormRequest;

class CreateBranchRequest extends FormRequest
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
            'name' => 'required|string|unique:branches,name',
            'city_id'      => 'required|integer|exists:cities,id',
            'zone_id'      => 'required|integer|exists:zones,id',
        ];
    }
}
