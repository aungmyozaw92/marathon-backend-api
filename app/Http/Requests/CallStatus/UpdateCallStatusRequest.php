<?php

namespace App\Http\Requests\CallStatus;

use App\Http\Requests\FormRequest;

class UpdateCallStatusRequest extends FormRequest
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
            'status' => 'required|string|unique:call_statuses,status,' . $this->route('call_status')->id,
            'status_mm' => 'required|string|unique:call_statuses,status_mm,' . $this->route('call_status')->id,
        ];
    }

    // public function updateCallStatus($callStatus)
    // {
    //     $callStatus->status = $this->status;
    //     $callStatus->status_mm = $this->status_mm;

    //     if($callStatus->isDirty()) {
    //         $callStatus->save();
    //     }
        
    //     return $callStatus;
    // }
}
