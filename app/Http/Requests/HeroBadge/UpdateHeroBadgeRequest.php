<?php

namespace App\Http\Requests\HeroBadge;

use Illuminate\Http\Request;
use App\Http\Requests\FormRequest;

class UpdateHeroBadgeRequest extends FormRequest
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
            'name'                  => 'required|string|unique:hero_badges,name,' . $this->route('hero_badge')->id,
            'logo'                  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description'           => 'required|string',
            'multiplier_point'      => 'required|regex:/^\d{1,12}(\.\d{1,4})?$/',
            'maintainence_point'    => 'required|regex:/^\d{1,12}(\.\d{1,4})?$/',
        ];
    }
}
