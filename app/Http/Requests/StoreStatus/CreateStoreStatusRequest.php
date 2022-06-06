<?php

namespace App\Http\Requests\StoreStatus;

use App\Models\StoreStatus;
use App\Http\Requests\FormRequest;

class CreateStoreStatusRequest extends FormRequest
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
            'status' => 'required|string|unique:store_statuses,status',
            'status_mm' => 'required|string|unique:store_statuses,status_mm'
        ];
    }

    // public function storeStoreStatus()
    // {
    //     return StoreStatus::create([
    //                 'status' => $this->status,
    //                 'status_mm' => $this->status_mm
    //             ]);
    // }
}
