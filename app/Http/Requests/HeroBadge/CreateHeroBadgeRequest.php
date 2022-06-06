<?php

namespace App\Http\Requests\HeroBadge;

use App\Models\HeroBadge;
use App\Http\Requests\FormRequest;

class CreateHeroBadgeRequest extends FormRequest
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
            'name'                  => 'required|string|unique:hero_badges,name',
            'logo'                  => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description'           => 'required|string',
            'multiplier_point'      => 'required|regex:/^\d{1,12}(\.\d{1,4})?$/',
            'maintainence_point'    => 'required|regex:/^\d{1,12}(\.\d{1,4})?$/',
        ];
    }
}
