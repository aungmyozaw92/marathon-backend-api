<?php

namespace App\Http\Requests\Meta;

use App\Models\Meta;
use App\Http\Requests\FormRequest;

class CreateMetaRequest extends FormRequest
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
            'key'   => 'required|string|unique:metas,key',
            'value' => 'required|string'
        ];
    }

    public function storeMeta()
    {
        return Meta::create([
                    'key'   => $this->key,
                    'value' => $this->value,
                    'created_by' => auth()->user()->id
                ]);
    }
}
