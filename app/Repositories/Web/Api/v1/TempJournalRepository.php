<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\TempJournal;
use App\Repositories\BaseRepository;

class TempJournalRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return TempJournal::class;
    }

    /**
     * @param array $data
     *
     * @return TempJournal
     */
    public function create_temp_journal($voucher, array $data) : TempJournal
    {
        if($voucher->parcels->count() == 1 ){
            $weight = $voucher->parcels[0]->weight ;
        }else{
            $weight = 0;
            foreach($voucher->parcels as $par){
                $weight += $par->weight;
            }
        }
        
        return TempJournal::create([
            'merchant_id' => isset($data['merchant_id']) ? $data['merchant_id'] : null,
            'debit_account_id' => $data['debit_account_id'],
            'credit_account_id' => $data['credit_account_id'],
            'resourceable_type' => $data['type'],
            'resourceable_id' => $data['resourceable_id'],
            'amount' => $data['amount'],
            'status' => isset($data['status']) ? $data['status'] : 0,
            'balance_status' => isset($data['balance_status']) ? $data['balance_status'] : 0,
            'thirdparty_invoice' => $voucher->thirdparty_invoice,
            'voucher_no' => $voucher->voucher_invoice,
            'pickup_date' => $voucher->pickup->pickup_date,
            'delivered_date' => ($voucher->delivered_date) ? $voucher->delivered_date : date('Y-m-d H:i:s'),
            'receiver_name' => $voucher->receiver_name,
            'receiver_address' => $voucher->receiver_address,
            'receiver_phone' => $voucher->receiver_phone, 
            'receiver_city' => $voucher->receiver_city->name,
            'receiver_zone' => ($voucher->receiver_zone) ? $voucher->receiver_zone->name : null,
            'total_amount_to_collect' => $voucher->total_amount_to_collect,
            'voucher_remark' => $voucher->remark,
            'delivery_status_id' => $voucher->delivery_status_id === 9 ? 9 : 8,
            'delivery_status' => $voucher->delivery_status_id === 9 ? 'Return' : 'Delivered',
            'weight' => $weight,
            'city_id' => auth()->user()->city_id,
            'created_by' => auth()->user() ? auth()->user()->id : 1
        ]);
    }

    /**
     * @param TempJournal  $temp_journal
     * @param array $data
     *
     * @return mixed
     */
    public function update_delivery_date(TempJournal $temp_journal)
    {
        $temp_journal->delivered_date = date('Y-m-d');
        $temp_journal->balance_status = 1;

        if($temp_journal->isDirty()) {
            $temp_journal->save();
        }

        return $temp_journal->refresh();
    }
    
}
