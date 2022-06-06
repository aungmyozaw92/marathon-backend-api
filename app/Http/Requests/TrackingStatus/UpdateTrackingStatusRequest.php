<?php

namespace App\Http\Requests\TrackingStatus;

use App\Http\Requests\FormRequest;

class UpdateTrackingStatusRequest extends FormRequest
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
            'status_en' => 'required|string|unique:tracking_statuses,status,' . $this->route('tracking_status')->id,
            'status_mm' => 'required|string|unique:tracking_statuses,status_mm,' . $this->route('tracking_status')->id,
        ];
    }
}
