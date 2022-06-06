<?php

namespace App\Repositories\Mobile\Api\v1\Operation;

use App\Models\Staff;
use App\Models\Voucher;
use App\Models\DeliSheet;
use App\Models\BusStation;
use App\Models\WaybillVoucher;
use App\Models\DeliSheetVoucher;
use App\Repositories\BaseRepository;

class DeliSheetRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return DeliSheet::class;
    }

    /**
     * @param array $data
     *
     * @return DeliSheet
     */
    public function create(array $data): DeliSheet
    {
        $delivery = Staff::findOrFail($data['delivery_id']);
        $deliSheet =  DeliSheet::create([
            'qty'         => 0,
            'zone_id'     => $delivery->zone_id,
            'delivery_id' => $data['delivery_id'],
            'staff_id'    => auth()->user()->id,
            'note'        => isset($data['note']) ? getConvertedString($data['note']) : null,
            'date'        => isset($data['date']) ? $data['date'] : date('Y-m-d H:i:s'),
            // 'priority'    => $data['priority'],
            'created_by'  => auth()->user()->id,
            'courier_type_id'  => isset($data['courier_type_id']) ? getConvertedString($data['courier_type_id']) : null,
            'is_commissionable'  => isset($data['is_commissionable']) ? getConvertedString($data['is_commissionable']) : 0,
        ]);

        return $deliSheet->refresh();
    }

    /**
     * @param array $data
     *
     * @return DeliSheet
     */
    public function assign_voucher(DeliSheet $deliSheet, array $data)
    {
        $voucher = Voucher::where('voucher_invoice', $data['voucher_no'])->firstOrFail();
        $responses = ['status' => 2,];
        $delisheet_voucher = null;

        $delisheet_voucher = DeliSheetVoucher::whereVoucherId($voucher->id)->orderBy('id', 'DESC')->first();
        $deli_voucher_check = true;

        if ($delisheet_voucher) {
            if ($delisheet_voucher->delivery_status) {
                $responses['message'] = "Voucher is already delivered";
                $deli_voucher_check = false;
            } elseif ($delisheet_voucher->return) {
                $responses['message'] = "Voucher is already returned.";
                $deli_voucher_check = false;
            } elseif (!$delisheet_voucher->cant_deliver) {
                $responses['message'] = "Voucher is already assigned to Delisheet";
                $deli_voucher_check = false;
            }
        }

        // $waybill_exists = WaybillVoucher::where('voucher_id', $voucher->id)->exists();
        // if ($waybill_exists) {
        //     $responses['message'] = "Voucher is already assigned to Waybill.";
        // }

        if (!$deliSheet->is_closed && $deli_voucher_check) {
            $deliSheet->vouchers()->attach($voucher->id);
            $deliSheet->qty += 1;
            $deliSheet->save();
            $deliSheet->delisheetVoucherFire($voucher->voucher_invoice, 'new_delisheet_voucher');
            $voucher->outgoing_status = 0;
            if ($voucher->delivery_counter == 0) {
                $voucher->delivery_status_id = 2;
            } elseif ($voucher->delivery_counter == 1) {
                $voucher->delivery_status_id = 3;
            } elseif ($voucher->delivery_counter >= 2) {
                $voucher->delivery_status_id = 4;
            }
            $voucher->delivery_counter += 1;
            $voucher->store_status_id = 5;
            $voucher->save();
            $voucher->voucherSheetFire($deliSheet->delisheet_invoice, 'new_delisheet_voucher');
            $responses = [
                'status' => 1,
                'message' => 'Success',
                'voucher' => $voucher
            ];
        }
        return $responses;
    }

    /**
     * @param DeliSheet  $deliSheet
     * @param array $data
     *
     * @return mixed
     */
    public function remove_vouchers(DeliSheet $deliSheet, array $data): DeliSheet
    {
        $voucher = Voucher::where('voucher_invoice', $data['voucher_no'])->firstOrFail();
        $qty = $deliSheet->qty;
        $deleted = $deliSheet->vouchers()->detach($voucher->id);

        if ($deleted) {
            $voucher->outgoing_status = null;
            $voucher->store_status_id = 4;
            $voucher->delivery_counter -= 1;
            $qty -= 1;
        }

        $deliSheet->qty = $qty;

        if ($voucher->isDirty()) {
            $voucher->updated_by = auth()->user()->id;
            $voucher->save();
            $voucher->voucherSheetFire($deliSheet->delisheet_invoice, 'remove_delisheet_voucher');
        }

        if ($deliSheet->isDirty()) {
            $deliSheet->updated_by = auth()->user()->id;
            $deliSheet->save();
            $deliSheet->delisheetVoucherFire($voucher->voucher_invoice, 'remove_delisheet_voucher');
        }

        return $deliSheet->refresh();
    }
}
