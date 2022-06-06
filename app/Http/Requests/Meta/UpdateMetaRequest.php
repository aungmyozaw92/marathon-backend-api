<?php

namespace App\Http\Requests\Meta;

use App\Http\Requests\FormRequest;

class UpdateMetaRequest extends FormRequest
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
            'key' => 'required|string|unique:metas,key,' . $this->route('meta')->id,
            'value' => 'required|string'
        ];
    }

    public function updateMeta($meta)
    {
        $meta->key = $this->key;
        $meta->value = $this->value;

        if($meta->isDirty()) {
            $meta->updated_by = auth()->user()->id;
            $meta->save();
        }
        
        return $meta;
    }
}
