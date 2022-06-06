<?php

namespace App\Repositories\Mobile\Api\v1\Delivery;

use App\Models\Staff;
use App\Models\Voucher;
use App\Repositories\BaseRepository;

class DeliveryVoucherRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Voucher::class;
    }

    public function getDeliVoucher()
    {
        $delivery = Staff::findOrFail(auth()->user()->id);
        $deli_vouchers = $delivery->deli_sheets()->with('vouchers')->get()->pluck('vouchers')->collapse()->unique('id')->values();
        $bus_vouchers = $delivery->bus_sheets()->with('vouchers')->get()->pluck('vouchers')->collapse()->unique('id')->values();
        $vouchers = $deli_vouchers->merge($bus_vouchers);
        return $vouchers;
    }
}
