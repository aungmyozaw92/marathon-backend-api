<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Account;
use App\Models\Journal;
use App\Models\Voucher;
use App\Models\Merchant;
use App\Models\AgentSheet;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\AccountRepository;
use App\Repositories\Web\Api\v1\JournalRepository;

class AgentSheetRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return AgentSheet::class;
    }

    /**
     * @param array $data
     *
     * @return AgentSheet
     */
    public function create(array $data) : AgentSheet
    {
        $agentSheet =  AgentSheet::create([
            'merchant_id' => $data['merchant_id'],
            // 'merchant_associate_id' => $data['merchant_associate_id'],
            'qty' => $data['qty'],
            'created_by'  => auth()->user()->id
        ]);

        $agentSheet->vouchers()->syncWithoutDetaching($data['voucher_id']);
        $total_debit = 0;
        $total_credit = 0;
        $total_balance = 0;
        // $qty = 0;
        foreach ($data['voucher_id'] as $voucher) {
            $voucher = Voucher::findOrFail($voucher);
            $voucher->outgoing_status = 4;
            $voucher->save();
            $merchant = Merchant::findOrFail($agentSheet->merchant_id);
            $debit  = $voucher->journals->where('status', 0)
                          ->where('debit_account_id', $merchant->account->id)
                          ->where('resourceable_id', $voucher->id)->sum('amount');
            $credit  = $voucher->journals->where('status', 0)
                          ->where('credit_account_id', $merchant->account->id)
                          ->where('resourceable_id', $voucher->id)->sum('amount');
            $total_debit += $debit;
            $total_credit += $credit;
            $balance = $credit - $debit;
            //$total_balance += $balance;
            // $qty += 1;

            //$voucher->journals()->where('status', 0)->update(['status' => 1]);
        }
        $agentSheet->credit = $total_credit;
        $agentSheet->debit = $total_debit;
        $agentSheet->balance = $total_balance;
        //$agentSheet->qty = $qty;
        $agentSheet->save();

        return $agentSheet->refresh();
    }

    /**
     * @param AgentSheet  $agentSheet
     * @param array $data
     *
     * @return mixed
     */

    // public function filterVoucher($filter)
    // {
    //     if (isset($filter['pickup_id']) && $pickup_id = $filter['pickup_id']) {
    //         $vouchers = Voucher::where('pickup_id', $pickup_id)->where('is_closed', 1)->get();
    //     } elseif (isset($filter['merchant_id']) && $merchant_id = $filter['merchant_id']) {
    //         $merchant = Merchant::findOrFail($merchant_id);
            
    //         if (isset($filter['merchant_associate_id']) && $merchant_associate_id = $filter['merchant_associate_id']) {
    //             $pickups = $merchant->pickups->where('sender_associate_id', $merchant_associate_id)->pluck('id');
    //         } else {
    //             $pickups = $merchant->pickups->pluck('id');
    //         }
    //         $vouchers = Voucher::whereIn('pickup_id', $pickups->all())->where('is_closed', 1)->get();
    //         //$vouchers = $merchant->pickups()->with('vouchers')->get()->pluck('vouchers')->collapse()->unique('id')->values();
    //     }
    //     //  dd($vouchers);
    //     if (isset($filter['delivery_status_id']) && $delivery_status_id = $filter['delivery_status_id']) {
    //         $vouchers = $vouchers->where('delivery_status_id', $delivery_status_id)->where('is_closed', 1);
    //     }

    //     return $vouchers;
    // }
}
