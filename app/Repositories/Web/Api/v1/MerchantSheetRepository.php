<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Account;
use App\Models\Journal;
use App\Models\Voucher;
use App\Models\Merchant;
use App\Models\MerchantSheet;
use App\Models\MerchantSheetVoucher;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\AccountRepository;
use App\Repositories\Web\Api\v1\JournalRepository;

class MerchantSheetRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return MerchantSheet::class;
    }

    /**
     * @param array $data
     *
     * @return MerchantSheet
     */
    public function create(array $data): MerchantSheet
    {
        $merchantSheet =  MerchantSheet::create([
            'merchant_id' => $data['merchant_id'],
            // 'merchant_associate_id' => $data['merchant_associate_id'],
            'qty' => $data['qty'],
            'note'        => isset($data['note']) ? getConvertedString($data['note']) : null,
            'created_by'  => auth()->user()->id
        ]);

        $merchantSheet->vouchers()->syncWithoutDetaching($data['voucher_id']);
        $total_debit = 0;
        $total_credit = 0;
        $total_balance = 0;

        $merchant = Merchant::findOrFail($merchantSheet->merchant_id);

        $vouchers = Voucher::whereIn('id', $data['voucher_id'])->get();

        // $user = auth()->user();
        // $branch_account_id = $user->city->branch->account->id;
        // $hq_account = Account::where('accountable_type', 'HQ')->first();

        foreach ($vouchers as $voucher) {
            $voucher->outgoing_status = 4;
            $voucher->save();
            $debit  = $voucher->journals->where('status', 0)
                ->where('debit_account_id', $merchant->account->id)
                ->where('resourceable_id', $voucher->id)->sum('amount');
            $credit  = $voucher->journals->where('status', 0)
                ->where('credit_account_id', $merchant->account->id)
                ->where('resourceable_id', $voucher->id)->sum('amount');
            $total_debit += $debit;
            $total_credit += $credit;
            $balance = $debit - $credit;
            $total_balance += $balance;

            // will use later in paid payment for merchant sheet
            // $unpaid_journal = $voucher->journals->where('status', 0)->first();

            // if ($user->department_id != 2) {
            //     if ($unpaid_journal->debit_account_id == $hq_account->id) {
            //         $unpaid_journal->debit_account_id = $branch_account_id;
            //     } elseif ($unpaid_journal->credit_account_id == $hq_account->id) {
            //         $unpaid_journal->credit_account_id = $branch_account_id;
            //     }
            // }
            // $unpaid_journal->save();
        }

        $merchantSheet->credit = $total_credit;
        $merchantSheet->debit = $total_debit;
        $merchantSheet->balance = $total_balance;
        //$merchantSheet->qty = $qty;
        $merchantSheet->save();
        $voucher->voucherSheetFire($merchantSheet->merchantsheet_invoice, 'new_msf_voucher');
        return $merchantSheet->refresh();
    }

    /**
     * @param ReturnSheet  $returnSheet
     * @param array $data
     *
     * @return mixed
     */
    public function remove_vouchers(MerchantSheet $merchantSheet, array $data): MerchantSheet
    {
        if (isset($data['vouchers'])) {
            $total_debit = 0;
            $total_credit = 0;
            $qty = $merchantSheet->qty;
            $merchant = Merchant::findOrFail($merchantSheet->merchant_id);

            foreach ($data['vouchers'] as $voucherId) {
                $voucher = Voucher::findOrFail($voucherId['id']);

                $merchantSheetVoucher = MerchantSheetVoucher::where('merchant_sheet_id', $merchantSheet->id)
                    ->where('voucher_id', $voucher->id)
                    ->firstOrFail();
                $deleted = $merchantSheet->vouchers()->detach($voucherId['id']);

                if ($deleted) {
                    $voucher->outgoing_status = 3;
                    $qty -= 1;
                }

                $debit  = $voucher->journals->where('status', 0)
                    ->where('debit_account_id', $merchant->account->id)
                    ->where('resourceable_id', $voucher->id)->sum('amount');
                $credit  = $voucher->journals->where('status', 0)
                    ->where('credit_account_id', $merchant->account->id)
                    ->where('resourceable_id', $voucher->id)->sum('amount');
                $total_debit += $debit;
                $total_credit += $credit;

                if ($voucher->isDirty()) {
                    $voucher->updated_by = auth()->user()->id;
                    $voucher->save();
                }
                $voucher->voucherSheetFire($merchantSheet->merchantsheet_invoice, 'remove_msf_voucher');
            }
            $total_credit = $merchantSheet->credit - $total_credit;
            $total_debit = $merchantSheet->debit - $total_debit;

            $merchantSheet->credit = $total_credit;
            $merchantSheet->debit = $total_debit;
            $merchantSheet->balance = $total_debit - $total_credit;
            $merchantSheet->qty = $qty;

            if ($merchantSheet->isDirty()) {
                $merchantSheet->updated_by = auth()->user()->id;
                $merchantSheet->save();
            }
            $merchantSheet->msfVoucherFire($voucher->voucher_invoice, 'remove_msf_voucher');
        }
        return $merchantSheet->refresh();
    }

    public function add_vouchers(MerchantSheet $merchantSheet, array $data): MerchantSheet
    {
        if (isset($data['vouchers'])) {
            $total_debit = 0;
            $total_credit = 0;
            $total_balance = 0;
            $qty = $merchantSheet->qty;
            $merchant = Merchant::findOrFail($merchantSheet->merchant_id);

            foreach ($data['vouchers'] as $voucher) {
                $merchantSheet->vouchers()->attach($voucher['id']);

                $voucher = Voucher::findOrFail($voucher['id']);
                $voucher->outgoing_status = 4;
                $voucher->save();
                $voucher->voucherSheetFire($merchantSheet->merchantsheet_invoice, 'new_msf_voucher');
                $qty += 1;

                $debit  = $voucher->journals->where('status', 0)
                    ->where('debit_account_id', $merchant->account->id)
                    ->where('resourceable_id', $voucher->id)->sum('amount');
                $credit  = $voucher->journals->where('status', 0)
                    ->where('credit_account_id', $merchant->account->id)
                    ->where('resourceable_id', $voucher->id)->sum('amount');
                $total_debit += $debit;
                $total_credit += $credit;
                $balance = $debit - $credit;
                $total_balance += $balance;
            }

            $total_credit = $merchantSheet->credit + $total_credit;
            $total_debit = $merchantSheet->debit + $total_debit;

            $merchantSheet->credit = $total_credit;
            $merchantSheet->debit = $total_debit;
            $merchantSheet->balance = $total_debit - $total_credit;
            $merchantSheet->qty = $qty;
        }

        if ($merchantSheet->isDirty()) {
            $merchantSheet->updated_by = auth()->user()->id;
            $merchantSheet->save();
        }
        $merchantSheet->msfVoucherFire($voucher->voucher_invoice, 'new_msf_voucher');
        return $merchantSheet->refresh();
    }

    /**
     * @param MerchantSheet  $merchantSheet
     * @param array $data
     *
     * @return mixed
     */
    public function update(MerchantSheet $merchantSheet, array $data): MerchantSheet
    {
        $merchantSheet->note = getConvertedString($data['note']);

        if ($merchantSheet->isDirty()) {
            $merchantSheet->updated_by = auth()->user()->id;
            $merchantSheet->save();
        }

        return $merchantSheet->refresh();
    }

    /**
     * @param MerchantSheet  $merchantSheet
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
