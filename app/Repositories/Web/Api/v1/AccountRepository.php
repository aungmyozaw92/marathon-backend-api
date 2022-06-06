<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Staff;
use App\Models\Pickup;
use App\Models\Account;
use App\Models\Journal;
use App\Models\Voucher;
use App\Models\Waybill;
use App\Models\BusSheet;
use App\Models\DeliSheet;
use App\Models\BranchSheet;
use App\Models\MerchantSheet;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\JournalRepository;
use App\Repositories\Web\Api\v1\TempJournalRepository;

class AccountRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Account::class;
    }

    /**
     * @param array $data
     *
     * @return Account
     */
    public function create(array $data): Account
    {
        return Account::create([
            //  'account_no' => $data['account_no'],
            'city_id'          => isset($data['city_id']) ? $data['city_id'] : null,
            'accountable_type' => $data['accountable_type'],
            'accountable_id'   => $data['accountable_id']
        ]);
    }

    /**
     * @param Account  $account
     * @param array $data
     *
     * @return mixed
     */
    public function update(Account $account, array $data) : Account
    {
        $account->city_id = isset($data['city_id']) ? $data['city_id'] : $account->city_id ;
        if ($account->isDirty()) {
            $account->save();
        }

        return $account->refresh();
    }

    public function update_balance(array $data)
    {
        if ($data['account_type'] == 'Agent') {
            $account_id = $data['agent_id'];
        } elseif ($data['account_type'] == 'Branch') {
            $account_id = $data['branch_id'];
        } else {
            $account_id = $data['merchant_id'];
        }
        $account = Account::where('accountable_type', $data['account_type'])->where('accountable_id', $account_id)->firstOrFail();
        // dd($account);
        $account->balance = $data['amount'];
        if ($account->isDirty()) {
            $account->save();
        }
        return $account->refresh();
    }

    public function delisheet_finance_confirm($data)
    {
        $deliSheet = DeliSheet::findOrFail($data['delisheet_id']);
        $responses = [
            'status' => 2,
        ];
        $deliSheet->is_closed == 0 ? $responses['message'] = "DeliSheet need to close first." :
            $responses['message'] = "DeliSheet is already paid.";

        $delivery_commission = 0;

        // if ($deliSheet->is_closed == 1 && $deliSheet->is_paid == 0) {
        if ($deliSheet->is_closed && !$deliSheet->is_paid && 
            $deliSheet->payment_token == $data['payment_token']) 
        {
            $deliSheet->is_paid = 1;
            if ($deliSheet->isDirty()) {
                $deliSheet->save();
                $deliSheet->refresh();
            }
            $vouchers = Voucher::whereIn('id', $data['vouchers_id'])->get();
            // $vouchers = Voucher::whereIn('id', $data['vouchers_id'])
            //                     ->with(['journals','journals.debit_account', 'journals.credit_account'])
            //                     ->get();
            // For delivered Voucher
            foreach ($vouchers as $voucher) {
                //if (!$voucher->is_return || $voucher->payment_type_id == 9 || $voucher->payment_type_id == 10) {
                if (!$voucher->is_return || $voucher->delivery_status_id == 8) {
                    if ($voucher->origin_city_id === $voucher->receiver_city_id && $voucher->origin_city_id != $voucher->sender_city_id) {
                        $this->confirm_branch_voucher($voucher);
                    } else {
                        $this->confirm_voucher($voucher);
                    }
                    $voucher->transaction_date = date('Y-m-d H:i:s');
                    $voucher->deli_payment_status = 1;
                    if ($voucher->isDirty()) {
                        $voucher->save();
                        // $voucher->refresh();
                    }
                }
                if ($voucher->delivery_commission > 0) {
                    $delivery_commission += $voucher->delivery_commission;
                }
            }


            //Calculate Staff commission and lunch
            $hero = Staff::findOrFail($deliSheet->delivery_id);
            if ($hero->is_commissionable && $deliSheet->is_commissionable) {
                $authBranchAccountId = auth()->user()->city->branch->account->id;
                $journalRepository = new JournalRepository();
                $hero_account = $hero->account ? $hero->account : $journalRepository->create_account($hero, 'Staff');
                $journalRepository->JournalCreateData($authBranchAccountId, $hero_account->id, $delivery_commission, $deliSheet, 'DeliSheet', 1);
            }
            $responses = [
                'status' => 1,
                'message' => 'DeliSheet is successfully confirm.'
            ];
        }

        return $responses;
    }

    public function pickup_finance_confirm($data)
    {
        $pickup = Pickup::findOrFail($data['pickup_id']);

        $journalRepository = new JournalRepository();
        // if ($pickup->pickuped_by_staff && $pickup->is_came_from_mobile) {
        //     if (isHero($pickup)) {
        //         // $commission_amount =  getPickupCommission();
        //         $hero = Staff::findOrFail($pickup->actby_mobile);
        //         $branch_account_id  = auth()->user()->city->branch->account->id;
        //         $hero_account = $hero->account ? $hero->account : $journalRepository->create_account($hero, 'Staff');
        //         if ($pickup->commission_amount > 0) {
        //             $this->JournalCreateData($branch_account_id, $hero_account->id, $pickup->commission_amount, $pickup, 'Pickup');
        //         }
        //     }
        // }

        $branch_account_id  = auth()->user()->city->branch->account->id;
        if ($pickup->pickup_fee > 0 && !$pickup->is_closed) {
            $account = $pickup->sender->account;
            if (!$account) {
                if ($pickup->sender_type == 'Merchant') {
                    $account = $journalRepository->create_account($pickup->sender, 'Merchant');
                } else {
                    $account = $journalRepository->create_account($pickup->sender, 'Customer');
                }
            }
            $this->JournalCreateData($account->id, $branch_account_id, $pickup->pickup_fee, $pickup, 'Pickup');
        }
        // For Prepaid Vouchers
        if (isset($data['prepaid_vouchers_id'])) {
            foreach ($data['prepaid_vouchers_id'] as $voucherId) {
                $voucher = Voucher::findOrFail($voucherId);
                $finance = $this->confirm_voucher($voucher);
            }
        }

        $pickup->is_paid = 1;
        $pickup->payment_receive_date = date('Y-m-d H:i:s');
        $pickup->payment_receive_by_type = 'Staff';
        $pickup->payment_receive_by_id = auth()->user()->id;

        if ($pickup->isDirty()) {
            $pickup->save();
        }

        return true;
    }

    public function waybill_finance_confirm($data)
    {
        $waybill = Waybill::findOrFail($data['waybill_id']);
        // dd($waybill->is_paid);
        if ($waybill->is_closed) {
            $waybill->is_paid = 1;
            if ($waybill->isDirty()) {
                $waybill->save();
            }
            // For delivered Voucher
            // foreach ($data['vouchers_id'] as $voucherId) {
            //     $voucher = Voucher::findOrFail($voucherId);
            //     if (!$voucher->is_return) {
            //        // $finance = $this->confirm_voucher($voucher);
            //     }
            // }
        } else {
            return false;
        }
        return true;
    }

    public function bus_sheet_finance_confirm($data)
    {
        $bus_sheet = BusSheet::findOrFail($data['bus_sheet_id']);

        $responses = [
            'status' => 2,
        ];
        $bus_sheet->is_closed == 0 ? $responses['message'] = "Bus Sheet need to close first." : $responses['message'] = "Bus Sheet is already paid.";

        if ($bus_sheet->is_closed == 1 && $bus_sheet->is_paid == 0) {
            $bus_sheet->is_paid = 1;
            if ($bus_sheet->isDirty()) {
                $bus_sheet->save();
            }
            // For delivered Voucher
            foreach ($data['vouchers_id'] as $voucherId) {
                $voucher = Voucher::findOrFail($voucherId);
                $finance = $this->confirm_voucher($voucher);
                $voucher->deli_payment_status = 1;
                if ($voucher->isDirty()) {
                    $voucher->save();
                }
            }

            $responses = [
                'status' => 1,
                'message' => 'Bus Sheet is successfully confirm.'
            ];
        }

        return $responses;
    }

    public function merchant_sheet_finance_confirm($data)
    {
        $merchantSheet = MerchantSheet::findOrFail($data['merchant_sheet_id']);
        $responses = [
            'status' => 2,
        ];

        if ($merchantSheet->is_paid == 0) {
            $merchantSheet->is_paid = 1;
            if ($merchantSheet->isDirty()) {
                $merchantSheet->save();
            }

            $user = auth()->user();
            $branch_account_id = $user->city->branch->account->id;
            $hq_account = Account::where('accountable_type', 'HQ')->firstOrFail();
            $journals = Journal::whereIn('resourceable_id', $data['vouchers_id'])
                ->where('resourceable_type', 'Voucher')->where('status', 0)->get();


            // For delivered Voucher
            foreach ($journals as $journal) {
                // branch or agent
                // if ($user->department_id != 2) {

                //     // have to pay merchant
                //     if ($journal->debit_account_id == $hq_account->id) {
                //         $journal->debit_account_id = $branch_account_id;

                //         $journal->debit_account->balance += $journal->amount;
                //         $journal->credit_account->balance += $journal->amount;
                //     } elseif ($journal->credit_account_id == $hq_account->id) { // merchant pays us
                //         $journal->credit_account_id = $branch_account_id;

                //         $journal->credit_account->balance -= $journal->amount;
                //         $journal->debit_account->balance -= $journal->amount;
                //     }
                // } else { // HQ
                //     if ($journal->debit_account_id == $hq_account->id) {
                //         $journal->debit_account->balance -= $journal->amount;
                //         $journal->credit_account->balance += $journal->amount;
                //     } elseif ($journal->credit_account_id == $hq_account->id) {
                //         $journal->credit_account->balance += $journal->amount;
                //         $journal->debit_account->balance -= $journal->amount;
                //     }
                // }

                $journal->status = 1;
                $journal->save();
                $journal->debit_account->save();
                $journal->credit_account->save();
            }

            Voucher::whereIn('id', $data['vouchers_id'])->update(['merchant_payment_status' => 1]);

            $responses = [
                'status' => 1,
                'message' => 'Merchant Sheet is successfully confirm.'
            ];
        } else {
            $responses['message'] = "Merchant Sheet is already paid.";
        }

        return $responses;
    }

    public function confirm_branch_voucher($voucher)
    {

        //$journals = Journal::where('resourceable_type', 'Voucher')->where('resourceable_id', $voucher->id)->get();
        $journals = $voucher->journals->where('status', 0);
        // $sender_delivery_commission = $voucher->sender_city->branch->delivery_commission;
        // $receiver_delivery_commission = $voucher->receiver_city->branch->delivery_commission;

        switch ($voucher->payment_type_id) {
                // Normal Delivery
            case 1: //Sum Total 4
                foreach ($journals as $journal) {
                    $debit_account_type = $journal->debit_account->accountable_type;
                    $credit_account = $journal->credit_account;
                    $credit_account_type = $credit_account->accountable_type;
                    if ($credit_account_type != 'Merchant') {

                        // Reduce paid amount from Customer's Balance
                        if ($debit_account_type == 'Customer') {
                            $journal->debit_account->balance -= $journal->amount;
                        }

                        // Reduce Collected Amount(remaining amount after reducing delivery_commission) from Branch/Agent Balance
                        if ($debit_account_type == 'Branch' || $debit_account_type == 'Agent') {
                            $journal->debit_account->balance -= $journal->amount;
                        }

                        // Add Delivery Amount(remaining amount after reducing delivery_commission) to HQ Balance
                        if ($debit_account_type == 'HQ') {
                            // if ($credit_account->city_id == $voucher->sender_city_id) {
                            //     $journal->debit_account->balance -= $journal->amount;
                            // }
                            $journal->credit_account->balance += $journal->amount;
                            $journal->credit_account->save();
                        }

                        // if ($credit_account_type == 'HQ') {
                        //     $journal->credit_account->balance += $voucher->total_delivery_amount;
                        //     $journal->credit_account->save();
                        // }
                        $journal->debit_account->save();
                    } else {
                        //$journal->debit_account->balance -= $journal->amount;
                        $journal->credit_account->balance += $journal->amount;
                        $journal->credit_account->save();
                    }
                    $this->update_journal_status($journal);
                }
                return true;
                break;
            case 2: //Net Total 4
                foreach ($journals as $journal) {
                    $debit_account_type = $journal->debit_account->accountable_type;
                    $credit_account = $journal->credit_account;
                    $credit_account_type = $credit_account->accountable_type;
                    if ($credit_account_type != 'Merchant') {

                        // Reduce paid amount from Customer's Balance
                        if ($debit_account_type == 'Customer') {
                            $journal->debit_account->balance -= $journal->amount;
                        }

                        // Reduce Collected Amount(remaining amount after reducing delivery_commission) from Branch/Agent Balance
                        if ($debit_account_type == 'Branch' || $debit_account_type == 'Agent') {
                            $journal->debit_account->balance -= $journal->amount;
                        }

                        // Add Delivery Amount(remaining amount after reducing delivery_commission) to HQ Balance
                        if ($debit_account_type == 'HQ') {
                            // if ($credit_account->city_id == $voucher->sender_city_id) {
                            //     $journal->debit_account->balance -= $journal->amount;
                            // }
                            $journal->credit_account->balance += $journal->amount;
                            $journal->credit_account->save();
                        }

                        // if ($credit_account_type == 'HQ') {
                        //     $journal->credit_account->balance += $voucher->total_delivery_amount;
                        //     $journal->credit_account->save();
                        // }

                        $journal->debit_account->save();
                    } else {
                        //$journal->debit_account->balance -= $journal->amount;
                        $journal->credit_account->balance += $journal->amount;
                        $journal->credit_account->save();
                    }
                    $this->update_journal_status($journal);
                }
                if ($voucher->payment_type_id === 2 && $voucher->pickup->sender_type === 'Merchant' && $voucher->pickup->merchant->is_corporate_merchant) {
                    $tempJournalRepository = new TempJournalRepository();
                    $tempJournalRepository->update_delivery_date($voucher->temp_journal);
                }
                return true;
                break;
            case 3: //Delivery Only 1
                foreach ($journals as $journal) {
                    $debit_account_type = $journal->debit_account->accountable_type;
                    $credit_account = $journal->credit_account;
                    $credit_account_type = $credit_account->accountable_type;

                    // Reduce paid amount from Customer's Balance
                    if ($debit_account_type == 'Customer') {
                        $journal->debit_account->balance -= $journal->amount;
                    }

                    // Reduce Collected Amount(remaining amount after reducing delivery_commission) from Branch/Agent Balance
                    if ($debit_account_type == 'Branch' || $debit_account_type == 'Agent') {
                        $journal->debit_account->balance -= $journal->amount;
                    }

                    // Add Delivery Amount(remaining amount after reducing delivery_commission) to HQ Balance
                    if ($debit_account_type == 'HQ') {
                        // if ($credit_account->city_id == $voucher->sender_city_id) {
                        //     $journal->debit_account->balance -= $journal->amount;
                        // }

                        $journal->credit_account->balance += $journal->amount;
                        $journal->credit_account->save();
                    }

                    // if ($credit_account_type == 'HQ') {
                    //     $journal->credit_account->balance += $voucher->total_delivery_amount;
                    //     $journal->credit_account->save();
                    // }

                    $journal->debit_account->save();
                    $this->update_journal_status($journal);
                }
                return true;
                break;
            case 4: //NTC
                foreach ($journals as $journal) {
                    $credit_account_type = $journal->credit_account->accountable_type;
                    $debit_account_type = $journal->debit_account->accountable_type;
                    if ($debit_account_type != 'Merchant') {

                        // Add Delivery commission amount to Branch/Agent Balance
                        if ($credit_account_type == 'Branch' || $credit_account_type == 'Agent') {
                            // $journal->debit_account->balance -= $journal->amount;
                            $journal->credit_account->balance += $journal->amount;
                            $journal->credit_account->save();
                            // $journal->debit_account->save();
                        }
                    }
                    if ($debit_account_type == 'Merchant') {
                        //$journal->credit_account->balance += $journal->amount;
                        $journal->debit_account->balance -= $journal->amount;
                        $journal->debit_account->save();
                    }
                    $this->update_journal_status($journal);
                }
                return true;
                break;
            case 5: //Unpaid Unpaid 1
                return true;
                break;
            case 7: //Paid Unpaid 1
                foreach ($journals as $journal) {
                    $this->update_journal_status($journal);
                }
                return true;
                break;
            case 6: //UnPaid Paid 3
                foreach ($journals as $key => $journal) {
                    $debit_account_type = $journal->debit_account->accountable_type;
                    if ($key == 1 && $debit_account_type == 'Merchant') {
                        $this->update_journal_status($journal);
                    }
                }
                return true;
                break;
            case 8: //Paid Paid 2
                foreach ($journals as $journal) {
                    $debit_account_type = $journal->debit_account->accountable_type;
                    $credit_account_type = $journal->credit_account->accountable_type;
                    if ($debit_account_type == 'Customer' || $debit_account_type == 'Merchant') {
                        $this->update_journal_status($journal);
                    }
                }
                return true;
                break;
            case 9: //Prepaid NTC 1
                foreach ($journals as $journal) {
                    $debit_account_type = $journal->debit_account->accountable_type;
                    $credit_account = $journal->credit_account;
                    $credit_account_type = $credit_account->accountable_type;

                    // if ($debit_account_type == 'Merchant' || $debit_account_type == 'Customer') {                    

                    // Reduce paid amount from Merchant's Balance
                    // if ($debit_account_type == 'Merchant') {
                    //     $journal->debit_account->balance -= $voucher->total_delivery_amount;
                    // }

                    // Add Delivery Commission to Branch/Agent Balance
                    if ($debit_account_type == 'Branch' || $debit_account_type == 'Agent') {
                        $journal->debit_account->balance -= $journal->amount;
                        //$journal->credit_account->balance += $voucher->total_delivery_amount;
                        $journal->credit_account->save();
                    }

                    if ($debit_account_type == 'HQ') {
                        // if ($credit_account->city_id == $voucher->sender_city_id) {
                        //     $journal->debit_account->balance -= $journal->amount;
                        // }

                        $journal->credit_account->balance += $journal->amount;
                        $journal->credit_account->save();
                    }

                    $journal->debit_account->save();
                    $this->update_journal_status($journal);
                }
                return true;
                break;
            case 10: //Prepaid Collect 3
                foreach ($journals as $journal) {
                    $debit_account_type = $journal->debit_account->accountable_type;
                    $credit_account = $journal->credit_account;
                    $credit_account_type = $credit_account->accountable_type;

                    if ($credit_account_type != 'Customer' && $credit_account_type != 'Merchant') {
                        // Reduce paid amount from Merchant's Balance
                        // if ($debit_account_type == 'Merchant') {
                        //     $journal->debit_account->balance -= $voucher->total_delivery_amount;
                        // }

                        // Reduce Delivery Fee from Customer's Balance
                        if ($debit_account_type == 'Customer') {
                            $journal->debit_account->balance -= $journal->amount;
                        }

                        // Add Delivery Commission to Branch/Agent Balance
                        if ($debit_account_type == 'Branch' || $debit_account_type == 'Agent') {
                            $journal->debit_account->balance -= $journal->amount;
                        }

                        if ($debit_account_type == 'HQ') {
                            // if ($credit_account->city_id == $voucher->sender_city_id) {
                            //     $journal->debit_account->balance -= $journal->amount;
                            // }

                            $journal->credit_account->balance += $journal->amount;
                            $journal->credit_account->save();
                        }

                        $journal->debit_account->save();
                    } elseif ($credit_account_type == 'Merchant') {
                        $journal->credit_account->balance += $journal->amount;
                        $journal->credit_account->save();
                    }
                    $this->update_journal_status($journal);
                }
                return true;
                break;
        }
    }
    public function confirm_voucher($voucher)
    {

        //$journals = Journal::where('resourceable_type', 'Voucher')->where('resourceable_id', $voucher->id)->get();
        $journals = $voucher->journals->where('status', 0);
        $branch_delivery_commission = auth()->user()->city->branch->delivery_commission;

        switch ($voucher->payment_type_id) {
                // Normal Delivery
            case 1: //Sum Total 4
                foreach ($journals as $journal) {
                    $debit_account_type = $journal->debit_account->accountable_type;
                    $credit_account_type = $journal->credit_account->accountable_type;
                    if ($credit_account_type != 'Merchant') {
                        // Reduce Collected Amount(remaining amount after reducing delivery_commission) from Branch/Agent Balance
                        if ($debit_account_type == 'Branch' || $debit_account_type == 'Agent') {
                            $journal->debit_account->balance -= $journal->amount - $branch_delivery_commission;
                        }

                        // Add Delivery Amount(remaining amount after reducing delivery_commission) to HQ Balance
                        // if ($debit_account_type == 'HQ') {
                        //     $journal->debit_account->balance += $voucher->total_delivery_amount - $branch_delivery_commission;
                        // }

                        // Reduce paid amount from Customer's Balance
                        if ($debit_account_type == 'Customer') {
                            $journal->debit_account->balance -= $journal->amount;
                        }
                    } else {
                        //$journal->debit_account->balance -= $journal->amount;
                        $journal->credit_account->balance += $journal->amount;
                        $journal->credit_account->save();
                    }

                    $journal->debit_account->save();

                    $this->update_journal_status($journal);
                }
                return true;
                break;
            case 2: //Net Total 4
                foreach ($journals as $journal) {
                    $debit_account_type = $journal->debit_account->accountable_type;
                    $credit_account_type = $journal->credit_account->accountable_type;
                    if ($credit_account_type != 'Merchant') {

                        // Reduce Collected Amount(remaining amount after reducing delivery_commission) from Branch/Agent Balance
                        if ($debit_account_type == 'Branch' || $debit_account_type == 'Agent') {
                            $journal->debit_account->balance -= $journal->amount - $branch_delivery_commission;
                        }

                        // Add Delivery Amount(remaining amount after reducing delivery_commission) to HQ Balance
                        // if ($debit_account_type == 'HQ') {
                        //     $journal->debit_account->balance += $voucher->total_delivery_amount - $branch_delivery_commission;
                        // }

                        // Reduce paid amount from Customer's Balance
                        if ($debit_account_type == 'Customer') {
                            $journal->debit_account->balance -= $journal->amount;
                        }
                    } else {
                        // $journal->debit_account->balance -= $journal->amount;
                        $journal->credit_account->balance += $journal->amount;
                        $journal->credit_account->save();
                    }
                    $journal->debit_account->save();
                    $this->update_journal_status($journal);
                }
                if ($voucher->payment_type_id === 2 && $voucher->pickup->sender_type === 'Merchant' && $voucher->pickup->merchant->is_corporate_merchant) {
                    $tempJournalRepository = new TempJournalRepository();
                    $tempJournalRepository->update_delivery_date($voucher->temp_journal);
                }
                return true;
                break;
            case 3: //Delivery Only 1
                foreach ($journals as $journal) {
                    $debit_account_type = $journal->debit_account->accountable_type;
                    $this->update_journal_status($journal);
                    // Reduce Collected Amount(remaining amount after reducing delivery_commission) from Branch/Agent Balance
                    if ($debit_account_type == 'Branch' || $debit_account_type == 'Agent') {
                        $journal->debit_account->balance -= $journal->amount - $branch_delivery_commission;
                    }

                    // Add Delivery Amount(remaining amount after reducing delivery_commission) to HQ Balance
                    // if ($debit_account_type == 'HQ') {
                    //     $journal->debit_account->balance += $voucher->total_delivery_amount - $branch_delivery_commission;
                    // }

                    // Reduce paid amount from Customer's Balance
                    if ($debit_account_type == 'Customer') {
                        $journal->debit_account->balance -= $journal->amount;
                    }

                    $journal->debit_account->save();
                }
                return true;
                break;
            case 4: //NTC
                foreach ($journals as $journal) {
                    $credit_account_type = $journal->credit_account->accountable_type;
                    $debit_account_type = $journal->debit_account->accountable_type;
                    // Add Delivery commission amount to Branch/Agent Balance
                    if ($debit_account_type == 'HQ' && ($credit_account_type == 'Branch' || $credit_account_type == 'Agent')) {
                        $journal->credit_account->balance += $branch_delivery_commission;

                        // $journal->debit_account->balance -= $branch_delivery_commission;
                        // $journal->debit_account->save();
                    }
                    if ($debit_account_type == 'Merchant' && $credit_account_type == 'HQ') {
                        //$journal->credit_account->balance += $journal->amount;
                        $journal->debit_account->balance -= $journal->amount;
                        $journal->debit_account->save();
                    }
                    $journal->credit_account->save();

                    $this->update_journal_status($journal);
                }
                return true;
                break;
            case 5: //Unpaid Unpaid 1
                return true;
                break;
            case 7: //Paid Unpaid 1
                foreach ($journals as $journal) {
                    $this->update_journal_status($journal);
                }
                return true;
                break;
            case 6: //UnPaid Paid 3
                foreach ($journals as $key => $journal) {
                    $debit_account_type = $journal->debit_account->accountable_type;
                    if ($key == 1 && $debit_account_type == 'Merchant') {
                        $this->update_journal_status($journal);
                    }
                }
                return true;
                break;
            case 8: //Paid Paid 2
                foreach ($journals as $journal) {
                    $debit_account_type = $journal->debit_account->accountable_type;
                    $credit_account_type = $journal->credit_account->accountable_type;
                    if ($debit_account_type == 'Customer' || $debit_account_type == 'Merchant') {
                        $this->update_journal_status($journal);
                    }
                }
                return true;
                break;
            case 9: //Prepaid NTC 1
                foreach ($journals as $journal) {
                    $debit_account_type = $journal->debit_account->accountable_type;

                    // if ($debit_account_type == 'Merchant' || $debit_account_type == 'Customer') {
                    $this->update_journal_status($journal);

                    // Add Delivery Amount(remaining amount after reducing delivery_commission) to HQ Balance
                    // if ($debit_account_type == 'HQ') {
                    //     $journal->debit_account->balance += $voucher->total_delivery_amount - $branch_delivery_commission;
                    // }

                    // Reduce paid amount from Merchant's Balance
                    // if ($debit_account_type == 'Merchant') {
                    //     $journal->debit_account->balance -= $voucher->total_delivery_amount;
                    // }

                    // Add Delivery Commission to Branch/Agent Balance
                    if ($debit_account_type == 'Branch' || $debit_account_type == 'Agent') {
                        $journal->debit_account->balance -= $journal->amount - $branch_delivery_commission;
                    }

                    $journal->debit_account->save();
                }
                return true;
                break;
            case 10: //Prepaid Collect 3
                foreach ($journals as $journal) {
                    $debit_account_type = $journal->debit_account->accountable_type;
                    $credit_account_type = $journal->credit_account->accountable_type;

                    if ($credit_account_type != 'Customer' && $credit_account_type != 'Merchant') {


                        // Add Delivery Amount(remaining amount after reducing delivery_commission) to HQ Balance
                        // if ($debit_account_type == 'HQ') {
                        //     $journal->debit_account->balance += $voucher->total_delivery_amount - $branch_delivery_commission;
                        // }

                        // Reduce paid amount from Merchant's Balance
                        // if ($debit_account_type == 'Merchant') {
                        //     $journal->debit_account->balance -= $voucher->total_delivery_amount;
                        // }

                        // Add Delivery Commission to Branch/Agent Balance
                        if ($debit_account_type == 'Branch' || $debit_account_type == 'Agent') {
                            $journal->debit_account->balance -= $journal->amount - $branch_delivery_commission;
                        }

                        // Reduce Delivery Fee from Customer's Balance
                        if ($debit_account_type == 'Customer') {
                            $journal->debit_account->balance -= $journal->amount;
                        }
                    }
                    if ($debit_account_type == 'HQ' && $credit_account_type == 'Merchant') {
                        $journal->credit_account->balance += $journal->amount;
                        //$journal->debit_account->balance -= $journal->amount;
                        $journal->credit_account->save();
                    }

                    $journal->debit_account->save();
                    $this->update_journal_status($journal);
                }
                return true;
                break;
        }
    }

    public function update_journal_status($journal)
    {
        $journalRepository = new JournalRepository();
        $data = $journalRepository->update_transaction_status($journal, 1);
        return $data;
    }

    public function reduce_debit_amount($journal)
    {
        //Reduce debit and balance
        $debit_account = $journal->debit_account;
        $debit_account->decrement('debit', $journal->amount);

        $debit_balance_amount = $debit_account->credit - $debit_account->debit;
        $debit_account->update(['balance' => $debit_balance_amount]);
    }

    public function reduce_credit_amount($journal)
    {
        //Reduce credit and balance
        $credit_account = $journal->credit_account;
        $credit_account->decrement('credit', $journal->amount);

        $credit_balance_amount = $credit_account->credit - $credit_account->debit;
        $credit_account->update(['balance' => $credit_balance_amount]);
    }

    public function increment_debit_amount($journal)
    {
        //Reduce debit and balance
        $debit_account = $journal->debit_account;
        $debit_account->increment('debit', $journal->amount);

        $debit_balance_amount = $debit_account->credit - $debit_account->debit;
        $debit_account->update(['balance' => $debit_balance_amount]);
    }

    public function increment_credit_amount($journal)
    {
        //Reduce credit and balance
        $credit_account = $journal->credit_account;
        $credit_account->increment('credit', $journal->amount);

        $credit_balance_amount = $credit_account->credit - $credit_account->debit;
        $credit_account->update(['balance' => $credit_balance_amount]);
    }

    public function JournalCreateData($debit_account, $credit_account, $amount, $resource, $type)
    {
        $journalRepository = new JournalRepository();
        $journal['debit_account_id'] = $debit_account;
        $journal['credit_account_id'] = $credit_account;
        $journal['amount'] = $amount;
        $journal['type'] = $type;
        $journal['resourceable_id'] = $resource->id;
        $journal['status'] = 1;
        $journal['balance_status'] = 1;

        $journal = $journalRepository->create_journal($journal);

        // $journalRepository->update_deibt_amount($journal);
        // $journalRepository->update_credit_amount($journal);
    }

    public function update_successful_balance($journal, $payment_type_id)
    {
        $journal->status = 1;
        $journal->balance_status = 1;
        $journal->credit_account->balance += $journal->amount;
        $journal->credit_account->save();
        // if ($payment_type_id <= 4) {
        //     $journal->debit_account->balance -= $journal->amount;
        //     $journal->debit_account->save();
        // }
        $journal->save();
    }

    public function manual_delisheet_finance_confirm($data)
    { 
            $voucher = Voucher::find($data['id']);
            if ($voucher->origin_city_id === $voucher->receiver_city_id && $voucher->origin_city_id != $voucher->sender_city_id) {
                $this->confirm_branch_voucher($voucher);
            } else {
                $this->confirm_voucher($voucher);
            }


            $responses = [
                'status' => 1,
                'message' => 'DeliSheet is successfully confirm.'
            ];
        

        return $responses;
    }

    // public function branch_sheet_finance_confirm($data)
    // {
    //     $branchSheet = BranchSheet::findOrFail($data['branch_sheet_id']);
    //     $responses = [
    //         'status' => 2,
    //     ];

    //     if ($branchSheet->is_paid == 0) {
    //         $branchSheet->is_paid = 1;
    //         if ($branchSheet->isDirty()) {
    //             $branchSheet->save();
    //         }

    //         $branch_account = $branchSheet->branch->account->id;

    //         $vouchers = Voucher::whereIn('id', $data['voucher_id'])->get();

    //         // For delivered Voucher
    //         foreach ($vouchers as $voucher) {
    //             $voucher->journals()->where('status', 0)
    //                                 ->where('resourceable_type', 'Voucher')
    //                                 ->where('resourceable_id', $voucher->id)
    //                                 ->where('debit_account_id', $branch_account->id)
    //                                 ->update(['status' => 1]);

    //             $voucher->branch_payment_status = 1;
    //             $voucher->save();
    //         }

    //         $responses = [
    //             'status' => 1,
    //             'message' => 'Branch Sheet is successfully confirm.'
    //         ];
    //     } else {
    //         $responses['message'] = "Branch Sheet is already paid.";
    //     }

    //     return $responses;
    // }

    // public function confirm_voucherbackup($voucher)
    // {

    //     //$journals = Journal::where('resourceable_type', 'Voucher')->where('resourceable_id', $voucher->id)->get();
    //     $journals = $voucher->journals->where('status', 0);
    //     switch ($voucher->payment_type_id) {
    //         // Normal Delivery
    //         case 1: //Sum Total 4
    //             foreach ($journals as $journal) {
    //                 $debit_account_type = $journal->debit_account->accountable_type;
    //                 $credit_account_type = $journal->credit_account->accountable_type;
    //                 if ($debit_account_type == 'Customer' || $debit_account_type == 'Merchant') {
    //                     // dd("hi");
    //                     $this->update_journal_status($journal);
    //                 }
    //             }
    //             return true;
    //             break;
    //         case 2: //Net Total 3
    //             foreach ($journals as $journal) {
    //                 $debit_account_type = $journal->debit_account->accountable_type;
    //                 $credit_account_type = $journal->credit_account->accountable_type;
    //                 if ($debit_account_type == 'Customer') {
    //                     // dd("sdfsd");
    //                     $this->update_journal_status($journal);
    //                 }
    //             }
    //             return true;
    //             break;
    //         case 3: //Delivery Only 1
    //             foreach ($journals as $journal) {
    //                 $debit_account_type = $journal->debit_account->accountable_type;
    //                  if ($debit_account_type == 'Customer') {
    //                     $this->update_journal_status($journal);
    //                 }
    //             }
    //             return true;
    //             break;
    //         case 4: //NTC
    //             foreach ($journals as $journal) {
    //                 $debit_account_type = $journal->debit_account->accountable_type;
    //                 $credit_account_type = $journal->credit_account->accountable_type;
    //                 if ($debit_account_type == 'Customer') {
    //                     $this->update_journal_status($journal);
    //                 }
    //             }
    //             return true;
    //             break;
    //         case 5: //Unpaid Unpaid 1
    //             return true;
    //             break;
    //         case 7: //Paid Unpaid 1
    //             foreach ($journals as $journal) {
    //                 $this->update_journal_status($journal);
    //             }
    //             return true;
    //             break;
    //         case 6: //UnPaid Paid 3
    //             foreach ($journals as $key => $journal) {
    //                 $debit_account_type = $journal->debit_account->accountable_type;
    //                 if ($key == 1 && $debit_account_type == 'Merchant') {
    //                     $this->update_journal_status($journal);
    //                 }
    //             }
    //             return true;
    //             break;
    //         case 8: //Paid Paid 2
    //             foreach ($journals as $journal) {
    //                 $debit_account_type = $journal->debit_account->accountable_type;
    //                 $credit_account_type = $journal->credit_account->accountable_type;
    //                 if ($debit_account_type == 'Customer' || $debit_account_type == 'Merchant') {
    //                     $this->update_journal_status($journal);
    //                 }
    //             }
    //             return true;
    //             break;
    //         case 9: //Prepaid NTC 1
    //             foreach ($journals as $journal) {
    //                 $debit_account_type = $journal->debit_account->accountable_type;
    //                 if ($debit_account_type == 'Merchant') {
    //                     $this->update_journal_status($journal);
    //                 }
    //             }
    //             return true;
    //             break;
    //         case 10: //Prepaid Collect 3
    //             foreach ($journals as $journal) {
    //                 $debit_account_type = $journal->debit_account->accountable_type;
    //                 $credit_account_type = $journal->credit_account->accountable_type;

    //                 if ($debit_account_type == 'Customer' || $debit_account_type == 'Merchant') {
    //                     $this->update_journal_status($journal);
    //                 }
    //             }
    //             return true;
    //             break;
    //     }
    // }
}
