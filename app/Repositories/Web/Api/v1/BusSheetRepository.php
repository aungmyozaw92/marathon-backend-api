<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Journal;
use App\Models\Voucher;
use App\Models\BusSheet;
use App\Models\BusSheetVoucher;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\VoucherRepository;

class BusSheetRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return BusSheet::class;
    }

    /**
     * @param array $data
     *
     * @return BusSheet
     */
    public function create(array $data): BusSheet
    {
        $busSheet =  BusSheet::create([
            'from_bus_station_id' => $data['from_bus_station_id'],
            'qty'        => $data['vouchers_qty'],
            'delivery_id'  => $data['delivery_id'],
            'staff_id'     => isset($data['staff_id']) ? $data['staff_id'] : auth()->user()->id,
            'note'         => isset($data['note']) ? getConvertedString($data['note']) : null,
            'created_by'   => auth()->user()->id
        ]);

        // $busSheet->vouchers()->syncWithoutDetaching($data['voucher_id']);

        if (isset($data['vouchers'])) {
            foreach ($data['vouchers'] as $voucher) {
                $note = null;
                if (isset($voucher['bus_sheet_voucher_note'])) {
                    $note = getConvertedString($voucher['bus_sheet_voucher_note']);
                }

                $busSheet->vouchers()->attach($voucher['id'], [
                    'note' => $note,
                    'priority' => $voucher['bus_sheet_voucher_priority']
                ]);
                $voucher = Voucher::findOrFail($voucher['id']);
                $voucher->outgoing_status = 2;
                $voucher->delivery_status_id = 2;
                $voucher->save();
            }
        }

        // foreach ($data['voucher_id'] as $voucher) {
        //     $voucher = Voucher::findOrFail($voucher);
        //     $voucher->outgoing_status = 2;
        //     $voucher->delivery_status_id = 2;
        //     $voucher->save();
        // }

        return $busSheet->refresh();
    }

    /**
     * @param BusSheet  $busSheet
     * @param array $data
     *
     * @return mixed
     */
    public function update(BusSheet $busSheet, array $data): BusSheet
    {
        // $busSheet->from_bus_station_id = isset($data['from_bus_station_id']) ? $data['from_bus_station_id'] : $busSheet->from_bus_station_id;
        // $busSheet->vouchers_qty = isset($data['vouchers_qty']) ? $data['vouchers_qty'] : $busSheet->vouchers_qty;
        // $busSheet->delivery_id = isset($data['delivery_id']) ? $data['delivery_id'] : $busSheet->delivery_id;
        // $busSheet->staff_id = isset($data['staff_id']) ? $data['staff_id'] : $busSheet->staff_id;
        $busSheet->is_closed = 1;

        if ($data['vouchers']) {
            foreach ($data['vouchers'] as $voucherId) {
                $voucher = Voucher::findOrFail($voucherId['id']);

                if (!$voucher->is_closed) {
                    $voucherRepository = new VoucherRepository();
                    $voucher = $voucherRepository->closed($voucher);
                }

                if ($voucherId['actual_bus_fee'] == null) {
                    $voucher->outgoing_status = null;
                    $voucher->store_status_id = 4;
                } else {
                    $voucher->delivery_status_id = 8;
                    $voucher->delivered_date = date('Y-m-d H:i:s');                    
                }
                $voucher->save();

                if ($voucher->bus_station) {
                    // $busSheet->vouchers()->updateExistingPivot($voucherId['id'], [ 'actual_bus_fee' => $voucherId['actual_bus_fee'] ]);
                    // $busSheetVoucher = \DB::table('bus_sheet_vouchers')->where('voucher_id', $voucherId['id'])->get();
                    // $busSheetVoucher->actual_bus_fee = $voucherId['actual_bus_fee'];
                    // $busSheetVoucher->save();
                    $voucherId['actual_bus_fee'] == null
                        ? $busSheet->vouchers()->updateExistingPivot($voucherId['id'], [
                            'is_return' => 1,
                            'delivery_status_id' => 10
                        ])
                        : $busSheet->vouchers()->updateExistingPivot($voucherId['id'], [
                            'is_return' => 0,
                            'actual_bus_fee' => $voucherId['actual_bus_fee'],
                            'delivery_status_id' => 8
                        ]);
                    if ($voucher->payment_type_id == 6 && $voucher->payment_type_id == 8) {
                        $journal = $voucher->journals->where('resourceable_id', $voucher->id)
                            ->where('credit_account_id', $voucher->sender_gate->account->id)->first();

                        if ($journal) {
                            $journalRepository = new JournalRepository();
                            $d['status'] = 1;
                            $d['amount'] = $voucherId['actual_bus_fee'];
                            $journalRepository->update($journal, $d);
                        }
                    }
                }
            }
        }

        if ($busSheet->isDirty()) {
            $busSheet->updated_by = auth()->user()->id;
            $busSheet->save();
        }

        return $busSheet->refresh();
    }

    /**
     * @param BusSheet $busSheet
     */
    public function destroy(BusSheet $busSheet)
    {
        $deleted = $this->deleteById($busSheet->id);

        if ($deleted) {
            $busSheet->deleted_by = auth()->user()->id;
            $busSheet->save();
        }
    }

    /**
     * @param BusSheet  $busSheet
     * @param array $data
     *
     * @return mixed
     */
    public function remove_vouchers(BusSheet $busSheet, array $data): BusSheet
    {
        if (isset($data['vouchers'])) {
            foreach ($data['vouchers'] as $voucherId) {
                $voucher = Voucher::findOrFail($voucherId['id']);

                $qty = $busSheet->qty;

                $busSheetVoucher = BusSheetVoucher::where('bus_sheet_id', $busSheet->id)
                    ->where('voucher_id', $voucher->id)
                    ->firstOrFail();
                $deleted = $busSheet->vouchers()->detach($voucherId['id']);

                if ($deleted) {
                    $voucher->outgoing_status = null;
                    $voucher->store_status_id = 4;
                    $voucher->delivery_counter -= 1;
                    $qty -= 1;
                }

                $busSheet->qty = $qty;

                if ($voucher->isDirty()) {
                    $voucher->updated_by = auth()->user()->id;
                    $voucher->save();
                }

                if ($busSheet->isDirty()) {
                    $busSheet->updated_by = auth()->user()->id;
                    $busSheet->save();
                }
            }
        }

        return $busSheet->refresh();
    }

    public function add_vouchers(BusSheet $busSheet, array $data): BusSheet
    {
        if (isset($data['vouchers'])) {
            foreach ($data['vouchers'] as $voucher) {
                $note = null;
                if (isset($voucher['bus_sheet_voucher_note'])) {
                    $note = getConvertedString($voucher['bus_sheet_voucher_note']);
                }

                $busSheet->vouchers()->attach($voucher['id'], [
                    'note' => $note,
                    'priority' => $voucher['bus_sheet_voucher_priority']
                ]);
                $voucher = Voucher::findOrFail($voucher['id']);
                $voucher->outgoing_status = 2;
                $voucher->delivery_status_id = 2;
                $voucher->save();

                $busSheet->qty + 1;
                $busSheet->save();
            }
        }

        return $busSheet->refresh();
    }
}
