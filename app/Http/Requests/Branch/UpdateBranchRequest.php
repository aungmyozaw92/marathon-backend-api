<?php

namespace App\Http\Requests\Branch;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateBranchRequest extends FormRequest
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
            'name' => 'required|string|unique:branches,name,' . $this->route('branch')->id,
            'city_id'      => 'required|integer|exists:cities,id',
            'zone_id'      => 'required|integer|exists:zones,id',
        ];
    }
}
