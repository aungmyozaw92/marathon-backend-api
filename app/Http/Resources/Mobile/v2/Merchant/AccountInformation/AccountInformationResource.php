<?php

namespace App\Http\Resources\Mobile\v2\Merchant\AccountInformation;

use App\Http\Resources\Bank\BankResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'account_name'      => $this->account_name,
            'account_no'        => $this->account_no,
            'bank_name'         => $this->bank->name,
            'is_default'        => $this->is_default
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'status' => 1,
        ];
    }
}
