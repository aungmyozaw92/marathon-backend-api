<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Account;
use App\Models\Journal;
use App\Models\Voucher;
use App\Models\Merchant;
use App\Models\Attachment;
use App\Models\ReturnSheet;
use App\Models\ReturnSheetVoucher;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Web\Api\v1\AccountRepository;
use App\Repositories\Web\Api\v1\JournalRepository;
use App\Repositories\Web\Api\v1\CustomerRepository;
use App\Models\Staff;

class ReturnSheetRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return ReturnSheet::class;
    }

    /**
     * @param array $data
     *
     * @return ReturnSheet
     */
    public function create(array $data): ReturnSheet
    {
        $returnSheet =  ReturnSheet::create([
            'merchant_id' => $data['merchant_id'],
            'merchant_associate_id' => $data['merchant_associate_id'],
            'qty' => $data['qty'],
            'delivery_id'  => $data['delivery_id'],
            'created_by'  => auth()->user()->id,
            'courier_type_id'  => isset($data['courier_type_id']) ? $data['courier_type_id'] : null,
            'is_commissionable'  => isset($data['is_commissionable']) ? $data['is_commissionable'] : 0,
            'is_pointable'  => isset($data['is_pointable']) ? $data['is_pointable'] : 0
        ]);

        if (isset($data['vouchers'])) {
            foreach ($data['vouchers'] as $v) {
                $voucher = Voucher::find($v['id']);
                if ($voucher->total_coupon_amount > 0) {
                    $total_delivery_amount = $voucher->total_delivery_amount - $voucher->total_coupon_amount;
                } else {
                    $total_delivery_amount = $voucher->discount_type == "extra" ?
                        $voucher->total_delivery_amount + $voucher->total_discount_amount : $voucher->total_delivery_amount - $voucher->total_discount_amount;
                }
                $note = null;
                if (isset($v['return_sheet_voucher_note'])) {
                    $note = getConvertedString($v['return_sheet_voucher_note']);
                }

                $returnSheet->vouchers()->attach($v['id'], [
                    'note' => $note,
                    'priority' => $v['return_sheet_voucher_priority'],
                    'is_return_fee' => $v['return_status']
                ]);

                if ($v['return_status'] == 1 || $v['return_status'] == 5) {
                    if ($voucher->receiver_city_id != $voucher->sender_city_id) {
                        $voucher->return_fee = $total_delivery_amount;
                    } else {
                        $voucher->return_fee = $total_delivery_amount / 2;
                    }
                    $voucher->return_type = 'Normal Return';
                }

                //no return fee
                if ($v['return_status'] === 2) {
                    // $this->noReturnFee($voucher);
                    $voucher->return_fee = 0;
                    $voucher->return_type = 'No Return Fee';
                }
                //half return fee
                if ($v['return_status'] === 3) {
                    // $this->halfReturnFee($voucher);
                    $voucher->return_fee = $total_delivery_amount / 2;
                    $voucher->return_type = 'Half Return Fee';
                }
                //full return fee
                if ($v['return_status'] === 4) {
                    // $this->fullReturnFee($voucher);
                    $voucher->return_fee = $total_delivery_amount;
                    $voucher->return_type = 'Full Return Fee';
                }
                $voucher->outgoing_status = 5;
                $voucher->store_status_id = 9;
                $voucher->save();
            }
        }
        $returnSheet->save();
        return $returnSheet->refresh();
    }

    /**
     * @param ReturnSheet  $returnSheet
     * @param array $data
     *
     * @return mixed
     */
    public function remove_vouchers(ReturnSheet $returnSheet, array $data): ReturnSheet
    {
        if (isset($data['vouchers'])) {
            foreach ($data['vouchers'] as $voucherId) {
                $voucher = Voucher::findOrFail($voucherId['id']);

                // $qty = $returnSheet->qty;
                $returnSheetVoucher = ReturnSheetVoucher::where('return_sheet_id', $returnSheet->id)
                    ->where('voucher_id', $voucher->id)
                    ->firstOrFail();
                $deleted = $returnSheet->vouchers()->detach($voucherId['id']);

                if ($deleted) {
                    $voucher->store_status_id = 4;
                    $voucher->outgoing_status = null;
                    // $qty -= 1;
                }


                // $returnSheet->qty = $qty;

                if ($voucher->isDirty()) {
                    $voucher->updated_by = auth()->user()->id;
                    $voucher->save();
                    $voucher->voucherSheetFire($returnSheet->return_sheet_invoice, 'remove_return_voucher');
                }

                // if ($returnSheet->isDirty()) {
                //     $returnSheet->updated_by = auth()->user()->id;
                //     $returnSheet->save();
                //     $returnSheet->returnSheetVoucherFire($voucher->voucher_invoice, 'remove_return_voucher');
                // }
                $returnSheet->returnSheetVoucherFire($voucher->voucher_invoice, 'remove_return_voucher');
            }
            $returnSheet->qty = $returnSheet->vouchers()->count();
            if ($returnSheet->isDirty()) {
                $returnSheet->updated_by = auth()->user()->id;
                $returnSheet->save();
            }
        }
        return $returnSheet->refresh();
    }

    public function add_vouchers(ReturnSheet $returnSheet, array $data): ReturnSheet
    {
        if (isset($data['vouchers'])) {
            foreach ($data['vouchers'] as $voucher) {
                $note = null;
                if (isset($voucher['return_sheet_voucher_note'])) {
                    $note = getConvertedString($voucher['return_sheet_voucher_note']);
                }

                $returnSheet->vouchers()->attach($voucher['id'], [
                    'note' => $note,
                    'priority' => $voucher['return_sheet_voucher_priority'],
                    'is_return_fee' => $voucher['return_status']
                ]);

                $voucher = Voucher::findOrFail($voucher['id']);
                $voucher->outgoing_status = 5;
                $voucher->store_status_id = 9;

                // if ($v['return_status'] == 1 || $v['return_status'] == 5) {
                if ($voucher->receiver_city_id != $voucher->sender_city_id) {
                    $voucher->return_fee = $voucher->total_delivery_amount;
                } else {
                    $voucher->return_fee = $voucher->total_delivery_amount / 2;
                }
                $voucher->return_type = 'Normal Return';
                // }

                $voucher->save();

                // $returnSheet->qty = $returnSheet->qty + 1;

                if ($voucher->isDirty()) {
                    $voucher->updated_by = auth()->user()->id;
                    $voucher->save();
                    $voucher->voucherSheetFire($returnSheet->return_sheet_invoice, 'new_return_voucher');
                }

                // if ($returnSheet->isDirty()) {
                //     $returnSheet->updated_by = auth()->user()->id;
                //     $returnSheet->save();
                //     $returnSheet->returnSheetVoucherFire($voucher->voucher_invoice, 'new_return_voucher');
                // }
                $returnSheet->returnSheetVoucherFire($voucher->voucher_invoice, 'new_return_voucher');
            }
            $returnSheet->qty = $returnSheet->vouchers()->count();
            if ($returnSheet->isDirty()) {
                $returnSheet->updated_by = auth()->user()->id;
                $returnSheet->save();
            }
        }

        return $returnSheet->refresh();
    }

    public function closed(ReturnSheet $returnSheet, array $data)
    {
        if (isset($data['vouchers'])) {
            foreach ($data['vouchers'] as $v) {

                ReturnSheetVoucher::where('voucher_id',$v['id'])
                ->where('return_sheet_id',$returnSheet->id)->update(['is_return_fee' => $v['return_status']]);
                // $returnSheet->vouchers()->sync([
                //     $v['id'] => [
                //         'is_return_fee' => $v['return_status']
                //     ]
                // ]);

                $voucher = Voucher::findOrFail($v['id']);
                if ($voucher->total_coupon_amount > 0) {
                    $total_delivery_amount = $voucher->total_delivery_amount - $voucher->total_coupon_amount;
                } else {
                    $total_delivery_amount = $voucher->discount_type == "extra" ?
                        $voucher->total_delivery_amount + $voucher->total_discount_amount : $voucher->total_delivery_amount - $voucher->total_discount_amount;
                }
                // closed voucher
                if (!$voucher->is_closed && $voucher->delivery_status_id == 9) {
                    $voucherRepository = new VoucherRepository();
                    $voucherRepository->closed($voucher);
                    $this->return_journal_process($voucher , $returnSheet->merchant);
                }

                $voucher->outgoing_status = 5;
                $voucher->is_return = 1;
                $voucher->store_status_id = 9;
                $voucher->deli_payment_status = 1;
                $voucher->transaction_date = date('Y-m-d H:i:s');
                
                if ($v['return_status'] == 1 || $v['return_status'] == 5) {
                    if (!$returnSheet->merchant->is_corporate_merchant) {
                        if ($voucher->payment_type_id >= 9 && $voucher->receiver_city_id != $voucher->sender_city_id) {
                            $journalRepository = new JournalRepository();
                            $merchant_account_id = $returnSheet->merchant->account->id;

                            $journalRepository->JournalCreateData($merchant_account_id, getHqAccount()->id, 0, $voucher, 'Voucher', 1);
                        }
                    }else{
                        if($voucher->receiver_city_id == $voucher->sender_city_id){
                            $temp_journal = $voucher->temp_journal;
                            
                            $temp_journal->amount = $temp_journal->amount / 2 ;
                            $temp_journal->save();
                        }
                        
                    }
                }

                //no return fee
                if ($v['return_status'] === 2) {
                    if (!$returnSheet->merchant->is_corporate_merchant) {
                        $this->noReturnFee($voucher);
                    }else{
                        $temp_journal = $voucher->temp_journal;
                        $temp_journal->amount = 0;
                        $temp_journal->save();
                    }
                    $voucher->return_fee = 0;
                    $voucher->return_type = 'No Return Fee';
                }
                //half return fee
                if ($v['return_status'] === 3) {
                    if (!$returnSheet->merchant->is_corporate_merchant) {
                        $this->halfReturnFee($voucher);
                    }else{
                        $temp_journal = $voucher->temp_journal;
                        $temp_journal->amount = $temp_journal->amount / 2 ;
                        $temp_journal->save();
                    }
                    
                    $voucher->return_fee = $voucher->total_delivery_amount / 2;
                    $voucher->return_type = 'Half Return Fee';
                }
                //full return fee
                if ($v['return_status'] === 4) {
                    if (!$returnSheet->merchant->is_corporate_merchant) {
                        $this->fullReturnFee($voucher);
                    }
                    $voucher->return_fee = $voucher->total_delivery_amount;
                    $voucher->return_type = 'Full Return Fee';
                }

                $voucher->save();

                $merchant_account_id = $returnSheet->merchant->account->id;
                $journal =  Journal::getJournal($merchant_account_id, $voucher->id)->first();

                if ($journal) {
                    if ($journal->debit_account->accountable_type == 'Merchant') {
                        $journal->debit_account->balance -= $journal->amount;
                        $journal->debit_account->save();
                    } else {
                        $journal->credit_account->balance += $journal->amount;
                        $journal->credit_account->save();
                    }
                    $journal->status = 1;
                    $journal->balance_status = 1;
                    $journal->save();
                }

                $customerRepository = new CustomerRepository();
                $customer = $customerRepository->rate($voucher->receiver, 'return');
            }
            $returnSheet->is_closed = 1;
            // $returnSheet->is_paid = 1;
            $returnSheet->is_returned = 1;
            $returnSheet->closed_date = now();
            $returnSheet->closed_by = auth()->user()->id;

            $returnSheet->save();
            return $returnSheet->refresh();
        }
    }

    public function halfReturnFee($voucher)
    {
        $payment_id = $voucher->payment_type_id;
        if ($payment_id <= 4) {
            $journal = $voucher->journals()->where('status', 0)->first();
            $amount = $journal->amount;
            if ($voucher->receiver_city_id != $voucher->sender_city_id) {
                $journal->amount = $amount / 2;
            } else {
                $journal->amount = $amount;
            }
            if ($journal->isDirty()) {
                $journal->save();
            }
        } elseif ($payment_id >= 9) {
            if ($voucher->receiver_city_id != $voucher->sender_city_id) {
                $journal = $voucher->journals->where('status', 1)->last();

                $merchant_account_id = $voucher->pickup->sender->account->id;
                $amount = $journal->amount / 2;

                $journalRepository = new JournalRepository();
                $journalRepository->JournalCreateData($journal->credit_account_id, $merchant_account_id, $amount, $voucher, 'Voucher', 0);
                // }
            }
        }
    }

    public function fullReturnFee($voucher)
    {
        $journalRepository = new JournalRepository();
        $merchant_account_id = $voucher->pickup->sender->account->id;

        $payment_id = $voucher->payment_type_id;
        if ($payment_id <= 4) {
            $journal = $voucher->journals()->where('status', 0)->first();
            $amount = $journal->amount;
            if ($voucher->receiver_city_id != $voucher->sender_city_id) {
                $journal->amount = $amount;
            } else {
                $journal->amount = $amount * 2;
            }
            if ($journal->isDirty()) {
                $journal->save();
            }
        } elseif ($payment_id >= 9) {
            if ($voucher->receiver_city_id == $voucher->sender_city_id) {
                $journals = $voucher->journals->whereIn('status', [0, 1]);

                foreach ($journals as $journal) {
                    if (
                        $journal->debit_account->accountable_type == 'Merchant' ||
                        $journal->credit_account->accountable_type == 'Merchant'
                    ) {
                        if ($journal->debit_account->accountable_type == 'Merchant') {
                            $journal->amount = $journal->amount * 2;
                            $journal->status = 1;
                        } else {
                            $journal->delete($journal->id);
                        }
                    }
                    if ($journal->isDirty()) {
                        $journal->save();
                    }
                }
            }
            $journalRepository->JournalCreateData($merchant_account_id, getHqAccount()->id, 0, $voucher, 'Voucher', 1);
        }
    }

    public function noReturnFee($voucher)
    {
        $payment_id = $voucher->payment_type_id;

        if ($payment_id <= 4) {
            $journal = $voucher->journals()->where('status', 0)->first();
            $journal->amount = 0;
            if ($journal->isDirty()) {
                $journal->save();
            }
        } else {
            $journalRepository = new JournalRepository();
            $merchant_account_id = $voucher->pickup->sender->account->id;
            $journal = $voucher->journals->where('status', 1)->last();
            // dd($voucher->journals);
            if ($voucher->receiver_city_id == $voucher->sender_city_id) {
                $journals = $voucher->journals->whereIn('status', [0, 1]);
                //    echo '<pre>';
                //  echo $journals->count();

                //$amount = $journal->amount * 2;
                foreach ($journals as $journal) {
                    // echo 'merchant';echo '<br>';
                    // echo $journal;
                    if ($journal->debit_account->accountable_type == 'Merchant' || $journal->credit_account->accountable_type == 'Merchant') {
                        if ($journal->debit_account->accountable_type == 'Merchant') {

                            // echo '1';echo '<br>';
                            $journal->amount = $journal->amount * 2;
                        }
                        if ($journal->credit_account->accountable_type == 'Merchant') {
                            // echo '2';
                            $journal->amount = $journal->amount * 2;
                        }
                        if ($journal->isDirty()) {
                            $journal->save();
                        }
                        $journal->refresh();
                    }
                }
            } else {
                $journals = $voucher->journals->whereIn('status', [0, 1]);
                foreach ($journals as $journal) {
                    if ($journal->debit_account->accountable_type == 'Merchant' || $journal->credit_account->accountable_type == 'Merchant') {
                        $debit_account_id = $journal->credit_account_id;
                        $credit_account_id = $journal->debit_account_id;

                        $journal->debit_account_id = $debit_account_id;
                        $journal->credit_account_id = $credit_account_id;
                        $journal->status = 0;
                        $journal->balance_status = 0;

                        if ($journal->isDirty()) {
                            $journal->save();
                        }
                        $journal->refresh();
                    }
                }
                $journalRepository->JournalCreateData(getHqAccount()->id, $merchant_account_id, $journal->amount, $voucher, 'Voucher', 0);
            }
            // $journalRepository->JournalCreateData(getHqAccount()->id, $merchant_account_id, $journal->amount, $voucher, 'Voucher', 0);
        }
    }

    public function return_journal_process($voucher, $merchant)
    {
        $journalRepository = new JournalRepository();

        // $voucher->is_return = 1;
        // $merchant_account = $voucher->pickup->sender->account;
        $merchant_account = $merchant->account;
        $journals = $voucher->journals->where('status', 0);
        //dd($journals);
        foreach ($journals as $journal) {
            $debit_account_type = $journal->debit_account->accountable_type;
            $credit_account = $journal->credit_account;
            $credit_account_type = $credit_account->accountable_type;

            // if (($debit_account_type === 'Customer' || $debit_account_type === 'Branch' || $debit_account_type == 'Merchant') ||
            //     ($debit_account_type === 'HQ' && $credit_account_type === 'Merchant') ||
            //     ($debit_account_type == 'HQ' && $credit_account_type == 'Branch' && $credit_account->city_id != $voucher->sender_city_id)) {
            // }

            $journal->status = 2;
            $journal->balance_status = 2;
            if ($journal->isDirty()) {
                $journal->save();
            }
            $journal->refresh();

            // if ($voucher->sender_city_id != $voucher->origin_city_id) {
            if ($debit_account_type == 'HQ' && $credit_account_type == 'Branch' && $credit_account->city_id == $voucher->sender_city_id) {
                $accountRepository = new AccountRepository();
                $accountRepository->update_successful_balance($journal, $voucher->payment_type_id);
            }
            // }
        }

        if ($voucher->total_coupon_amount > 0) {
            $delivery_amount = $voucher->total_delivery_amount - $voucher->total_coupon_amount;
        } else {
            $delivery_amount = $voucher->discount_type == "extra" ?
                $voucher->total_delivery_amount + $voucher->total_discount_amount : $voucher->total_delivery_amount - $voucher->total_discount_amount;
        }
        $return_percentage = ($voucher->receiver_city_id != $voucher->sender_city_id) ? 100 : getReturnPercentage();
        $return_amount = $merchant->is_corporate_merchant ? 0 : $delivery_amount * ($return_percentage / 100);

        $branch_account =  ($voucher->sender_city->branch) ? $voucher->sender_city->branch->account : $voucher->from_agent->account;
        $hq_account =  $hq_account = Account::where('accountable_type', 'HQ')->firstOrFail();

        if ($voucher->payment_type_id == 9 || $voucher->payment_type_id == 10) {
            $journalRepository->JournalCreateData($branch_account->id, $hq_account->id, $delivery_amount, $voucher, 'Voucher', 1);

            if ($voucher->receiver_city_id == $voucher->sender_city_id) {
                $journalRepository->JournalCreateData($merchant_account->id, $branch_account->id, $return_amount, $voucher, 'Voucher', 1);
                $journalRepository->JournalCreateData($hq_account->id, $merchant_account->id, $return_amount, $voucher, 'Voucher');
            } else {
                // $journalRepository->JournalCreateData($merchant_account->id, $hq_account->id, $return_amount, $voucher, 'Voucher', 1);
            }

            // update branch and merchant balance because of prepaid type
            $branch_account->balance -= $delivery_amount;
            $branch_account->save();
        } else {
            $journalRepository->JournalCreateData($merchant_account->id, $hq_account->id, $return_amount, $voucher, 'Voucher');
        }
        return $voucher->refresh();
    }

    /**
     * Upload Attachment
     */
    public function upload($returnSheet, $file): ReturnSheet
    {
        /**
         * Check Request has File
         */
        $file_name = null;
        $folder  = 'return_sheet';
        $date_folder = date('F-Y');
        $path = $folder . '/' . $date_folder;
        if (gettype($file) == 'string') {
            $file_name = $returnSheet->return_sheet_invoice . '_image_' . time() . '.' . 'png';
            $file_content = base64_decode($file);
        } else {
            $file_name = $returnSheet->return_sheet_invoice . '_image_' . time() . '_' . $file->getClientOriginalName();
            $file_content = file_get_contents($file);
        }
        Storage::disk('dospace')->put($path . '/' . $file_name, $file_content);
        Storage::setVisibility($path . '/' . $file_name, "public");

        Attachment::create([
            'resource_type' => 'ReturnSheet',
            'image' => $file_name,
            'resource_id' => $returnSheet->id,
            'note' => $returnSheet->remark,
            'latitude' => null,
            'longitude' => null,
            'is_sign' => 0,
            'created_by' => auth()->user()->id
        ]);

        return $returnSheet->refresh();
    }

    /**
     * @param ReturnSheet $returnSheet
     */
    public function destroy(ReturnSheet $returnSheet)
    {
        $deleted = $returnSheet->delete();

        if ($deleted) {
            $returnSheet->deleted_by = auth()->user()->id;
            $returnSheet->save();
        }
    }
    // change or assign hero 
    public function change_hero(ReturnSheet $returnSheet, array $data): ReturnSheet
    {
        $delivery = Staff::find($data['delivery_id']);
        $returnSheet->delivery_id = $delivery->id;
        $returnSheet->courier_type_id = isset($data['courier_type_id']) ? $data['courier_type_id'] : $returnSheet->courier_type_id;
        $returnSheet->is_commissionable = isset($data['is_commissionable']) ? $data['is_commissionable'] : $returnSheet->is_commissionable;
        $returnSheet->is_pointable = isset($data['is_pointable']) ? $data['is_pointable'] : $returnSheet->is_pointable;

        if ($returnSheet->isDirty()) {
            $returnSheet->save();
        }

        return $returnSheet->refresh();
    }
}
