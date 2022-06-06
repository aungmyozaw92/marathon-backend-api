<?php

namespace App\Observers;

use App\Models\MerchantSheet;
use App\Models\MerchantSheetHistory;

class MerchantSheetObserver
{
    /**
     * Handle the deli sheet "created" event.
     *
     * @param  \App\MerchantSheet  $merchant_sheet
     * @return void
     */
    public function created(MerchantSheet $merchant_sheet)
    {
        $merchant_sheet->merchantsheet_invoice = $merchant_sheet->id;
        $merchant_sheet->save();
        $logStatusId = getStatusId('new_msf');
        MerchantSheetHistory::create([
            'merchant_sheet_id' => $merchant_sheet->id,
            'log_status_id' => $logStatusId,
            'created_by' => isset(auth()->user()->id) ? auth()->user()->id : 1
        ]);
    }

    /**
     * Handle the deli sheet "updated" event.
     *
     * @param  \App\MerchantSheet  $merchant_sheet
     * @return void
     */
    public function updated(MerchantSheet $merchant_sheet)
    {
        $changes = $merchant_sheet->getChanges();
        $expected_columns = [
            'is_paid',
            'note',
        ];
        $changes = array_only($changes, $expected_columns);
        $transformedInputs = [];
        foreach ($changes as $key => $value) {
            $previous = $merchant_sheet->getOriginal($key);
            $next = $value;
            $transformedInputs[] = transformedMerchantSheetsAttribute($key, $previous, $next);
        }

        foreach ($transformedInputs as $key => $value) {
            $logStatusId = getStatusId($value['status']);
            if ((
                    ($value['status'] == 'change_note')
                    &&
                    ($value['next'] != null))
                ||
                ($value['previous'] != null && $value['next'] != null)
                ||
                $value['status'] == 'receive_payment'
            ) {
                if (is_bool($value['previous'])) {
                    $value['previous'] = null;
                    $value['next'] = null;
                }
                MerchantSheetHistory::create([
                    'merchant_sheet_id' => $merchant_sheet->id,
                    'log_status_id' => $logStatusId,
                    'previous' => $value['previous'],
                    'next' => $value['next'],
                    'created_by' => auth()->user()->id
                ]);
            }
        }
    }

    /**
     * Handle the deli sheet "deleted" event.
     *
     * @param  \App\MerchantSheet  $merchant_sheet
     * @return void
     */
    public function deleted(MerchantSheet $merchant_sheet)
    {
        $logStatusId = getStatusId('delete_msf');

        MerchantSheetHistory::create([
            'merchant_sheet_id' => $merchant_sheet->id,
            'log_status_id' => $logStatusId,
            'previous' => $merchant_sheet->merchantsheet_invoice,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * Handle the deli sheet "restored" event.
     *
     * @param  \App\MerchantSheet  $merchant_sheet
     * @return void
     */
    public function restored(MerchantSheet $merchant_sheet)
    {
        //
    }

    /**
     * Handle the deli sheet "force deleted" event.
     *
     * @param  \App\MerchantSheet  $merchant_sheet
     * @return void
     */
    public function forceDeleted(MerchantSheet $merchant_sheet)
    {
        //
    }
}
