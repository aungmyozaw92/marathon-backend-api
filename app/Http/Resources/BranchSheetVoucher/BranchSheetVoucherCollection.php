<?php

namespace App\Http\Resources\BranchSheetVoucher;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\BranchSheetVoucher\BranchSheetVoucherResource;


class BranchSheetVoucherCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = BranchSheetVoucherResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function with($request)
    {
        return [
            'status' => 1,
        ];
    }
}
