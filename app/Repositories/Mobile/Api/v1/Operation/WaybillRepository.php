<?php

namespace App\Repositories\Mobile\Api\v1\Operation;

use App\Models\Voucher;
use App\Models\Waybill;
use App\Models\WaybillVoucher;
use App\Models\DeliSheetVoucher;
use App\Repositories\BaseRepository;

class WaybillRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Waybill::class;
    }

    /**
     * @param array $data
     *
     * @return Waybill
     */
    public function assign_voucher(Waybill $waybill, array $data)
    {
        $voucher = Voucher::where('voucher_invoice', $data['voucher_no'])->firstOrFail();

        $responses = ['status' => 2,];

        $delisheet_exists = DeliSheetVoucher::whereVoucherId($voucher->id)->exists();
        if ($delisheet_exists) {
            $responses['message'] = "Voucher is already assigned to Delisheet.";
        }

        $waybill_exists = WaybillVoucher::where('voucher_id', $voucher->id)->exists();
        if ($waybill_exists) {
            $responses['message'] = "Voucher is already assigned to Waybill.";
        }

        if ($waybill->to_city_id != $voucher->receiver_city_id) {
            $responses['message'] = "Pls select same city";
        }

        if (!$waybill->is_closed && !$delisheet_exists && !$waybill_exists && $waybill->to_city_id == $voucher->receiver_city_id) {
            $waybill->vouchers()->attach($voucher->id);
            $waybill->qty += 1;
            $waybill->save();
            $waybill->waybillVoucherFire($voucher->voucher_invoice, 'new_waybill_voucher');

            $voucher->outgoing_status = 1;
            $voucher->delivery_status_id = 2;
            $voucher->save();
            $voucher->voucherSheetFire($waybill->waybill_invoice, 'new_waybill_voucher');
            $responses = [
                'status' => 1,
                'message' => 'Success',
                'voucher' => $voucher
            ];
        }
        return $responses;
    }

    /**
     * @param Waybill  $waybill
     * @param array $data
     *
     * @return mixed
     */
    public function remove_vouchers(Waybill $waybill, array $data): Waybill
    {
        $voucher = Voucher::findOrFail($data['voucher_id']);

        $deleted = $waybill->vouchers()->detach($data['voucher_id']);

        if ($deleted) {
            $voucher->outgoing_status = null;
            $voucher->store_status_id = 4;
            $voucher->delivery_counter -= 1;
        }

        if ($voucher->isDirty()) {
            $voucher->updated_by = auth()->user()->id;
            $voucher->save();
            $voucher->voucherSheetFire($waybill->waybill_invoice, 'remove_waybill_voucher');
            $waybill->waybillVoucherFire($voucher->voucher_invoice, 'remove_waybill_voucher');
        }

        if ($waybill->isDirty()) {
            $waybill->updated_by = auth()->user()->id;
            $waybill->save();
        }
        return $waybill->refresh();
    }
}
