<?php

namespace App\Http\Requests\Mobile\Upload;

use App\Http\Requests\FormRequest;

class AttachmentUploadRequest extends FormRequest
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

          //  'file'          => 'required|string',
            'description'          => 'nullable|string',
            'voucher_id'               => 'required|integer|exists:vouchers,id',
        
        ];
    }
}
