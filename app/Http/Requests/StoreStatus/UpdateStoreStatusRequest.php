<?php

namespace App\Http\Requests\StoreStatus;

use App\Http\Requests\FormRequest;

class UpdateStoreStatusRequest extends FormRequest
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
            'status' => 'required|string|unique:store_statuses,status,' . $this->route('store_status')->id,
            'status_mm' => 'required|string|unique:store_statuses,status_mm,' . $this->route('store_status')->id,
        ];
    }

    // public function updateStoreStatus($storeStatus)
    // {
    //     $storeStatus->status = $this->status;
    //     $storeStatus->status_mm = $this->status_mm;

    //     if($storeStatus->isDirty()) {
    //         $storeStatus->save();
    //     }
        
    //     return $storeStatus;
    // }
}
