<?php

namespace App\Observers;

use App\Models\ReturnSheet;
use App\Models\ReturnSheetHistory;
use App\Services\FirebaseService;
class ReturnSheetObserver
{
    /**
     * Handle the deli sheet "created" event.
     *
     * @param  \App\ReturnSheet  $return_sheet
     * @return void
     */
	private $firebaseService;
	public function __construct(FirebaseService $firebaseService)
	{
		$this->firebaseService = $firebaseService;
	}
    public function created(ReturnSheet $return_sheet)
    {
        $return_sheet->return_sheet_invoice = $return_sheet->id;
        $return_sheet->save();
        $logStatusId = getStatusId('new_returnsheet');
        ReturnSheetHistory::create([
            'return_sheet_id' => $return_sheet->id,
            'log_status_id' => $logStatusId,
            'created_by' => isset(auth()->user()->id) ? auth()->user()->id : 1
        ]);
		if (isset($return_sheet->merchant)) {
			$deviceTokens = $return_sheet->merchant->device_tokens()->where('is_active', 1)->pluck('device_token')->toArray();
			if (!empty($deviceTokens)) {
				$payload = [
					'receiver' => $return_sheet->merchant->name,
					'device_tokens' => $deviceTokens,
					'type' => 'returning',
					'body' => 'ယနေ့ သင့်ထံသို့ ပြန်ပို့ရန်  ပါဆယ် ရှိပါသည်။',
					'invoice' => $return_sheet->return_sheet_invoice
				];
				$this->firebaseService->sendNotification($payload);	
			}
		}
    }

    /**
     * Handle the deli sheet "updated" event.
     *
     * @param  \App\ReturnSheet  $return_sheet
     * @return void
     */
    public function updated(ReturnSheet $return_sheet)
    {
        $changed_columns = $return_sheet->getChanges();
        $expected_columns = ['note', 'is_return_fee', 'priority'];
        $changes = array_only($changed_columns, $expected_columns);
		// if ($return_sheet->wasChanged('is_closed') && $return_sheet->is_closed) {
		// 	$this->firebaseService->cleanNotification(['receiver' => $return_sheet->merchant->name, 'invoice' => $return_sheet->return_sheet_invoice]);
		// }
        if (empty($changes)) return;
        $transformedInputs = [];
        foreach ($changes as $key => $value) {
            $previous = $return_sheet->getOriginal($key);
            $next = $value;
            $transformedInputs[] = transformedReturnSheetsAttribute($key, $previous, $next);
        }
        foreach ($transformedInputs as $key => $value) {
            $logStatusId = getStatusId($value['status']);
            if (
                (
                    ($value['status'] == 'change_note')
                    &&
                    ($value['next'] != null))
                ||
                ($value['previous'] != null && $value['next'] != null)
            ) {
                if (is_bool($value['previous'])) {
                    $value['previous'] = null;
                    $value['next'] = null;
                }
                ReturnSheetHistory::create([
                    'return_sheet_id' => $return_sheet->id,
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
     * @param  \App\ReturnSheet  $return_sheet
     * @return void
     */
    public function deleted(ReturnSheet $return_sheet)
    {
        $logStatusId = getStatusId('delete_returnsheet');

        ReturnSheetHistory::create([
            'return_sheet_id' => $return_sheet->id,
            'log_status_id' => $logStatusId,
            'previous' => $return_sheet->return_sheet_invoice,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * Handle the deli sheet "restored" event.
     *
     * @param  \App\ReturnSheet  $return_sheet
     * @return void
     */
    public function restored(ReturnSheet $return_sheet)
    {
        //
    }

    /**
     * Handle the deli sheet "force deleted" event.
     *
     * @param  \App\ReturnSheet  $return_sheet
     * @return void
     */
    public function forceDeleted(ReturnSheet $return_sheet)
    {
        //
    }
}
