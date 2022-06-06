<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Branch;
use App\Models\Voucher;
use App\Models\BranchSheet;
use App\Repositories\BaseRepository;

class BranchSheetRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return BranchSheet::class;
    }

    /**
     * @param array $data
     *
     * @return BranchSheet
     */
    public function create(array $data) : BranchSheet
    {
        $branchSheet =  BranchSheet::create([
            'branch_id' => $data['branch_id'],
            // 'merchant_associate_id' => $data['merchant_associate_id'],
            'qty' => $data['qty'],
            'created_by'  => auth()->user()->id
        ]);

        $branch = Branch::findOrFail($branchSheet->branch_id);
        
        $branchSheet->vouchers()->syncWithoutDetaching($data['voucher_id']);
        $total_debit = 0;
        $total_credit = 0;
        $total_balance = 0;

        $vouchers = Voucher::whereIn('id', $data['voucher_id'])->get();

        foreach ($vouchers as $voucher) {
            $voucher->outgoing_status = 4;
            $voucher->save();

            $debit  = $voucher->journals->where('status', 0)
                                ->where('debit_account_id', $branch->account->id)
                                ->where('resourceable_id', $voucher->id)->sum('amount');
            $credit  = $voucher->journals->where('status', 0)
                                ->where('credit_account_id', $branch->account->id)
                                ->where('resourceable_id', $voucher->id)->sum('amount');
            $total_debit += $debit;
            $total_credit += $credit;
            $balance = $debit - $credit;
            $total_balance += $balance;
        }

        $branchSheet->credit = $total_credit;
        $branchSheet->debit = $total_debit;
        $branchSheet->balance = $total_balance;
        //$branchSheet->qty = $qty;
        $branchSheet->save();

        return $branchSheet->refresh();
    }
}
