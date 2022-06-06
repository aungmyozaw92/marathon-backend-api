<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Gate;
use App\Models\Account;
use App\Models\Journal;
use App\Models\Customer;
use App\Models\Merchant;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\AccountRepository;
use App\Repositories\Web\Api\v1\TempJournalRepository;

class JournalRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Journal::class;
    }

    /**
     * @param array $data
     *
     * @return Journal
     */
    public function create($data)
    {
        $merchant_account = null;
        $sender_account = null;

        $pickup = $data->pickup;

        if ($pickup->sender_type === "Merchant") {
            // $merchant = Merchant::findOrFail($pickup->sender_id);
            // $customer = Customer::findOrFail($data->receiver_id);
            $merchant = $pickup->sender;
            $customer = $data->receiver;
            
            $merchant_account  = $merchant->account;
            $customer_account  = $customer->account;
            
            if (!$merchant_account) {
                $merchant_account = $this->create_account($merchant, 'Merchant');
            }
            if (!$customer_account) {
                $customer_account = $this->create_account($customer, 'Customer');
            }
        } else {
            $customer = Customer::findOrFail($pickup->sender_id);
            $sender_account  = $customer->account;
            
            if (!$sender_account) {
                $sender_account = $this->create_account($customer, 'Customer');
            }
            $receiver_customer = Customer::findOrFail($data->receiver_id);
            $customer_account  = $receiver_customer->account;
            if (!$customer_account) {
                $customer_account = $this->create_account($receiver_customer, 'Customer');
            }
        }
        //allocate For Branch to Branch Journal
        if ($data->sender_city_id != $data->receiver_city_id) {
            
            $journal = $this->allocateBranchToBranchJournal($data, $merchant_account, $customer_account, $sender_account);
            
        } else {
            $journal = $this->allocateJournal($data, $merchant_account, $customer_account, $sender_account);
        }
        
        return $journal;
    }

    public function update(Journal $journal, array $data): Journal
    {
        $journal->amount = isset($data['amount']) ? $data['amount'] : $journal->amount;
        $journal->status = isset($data['status']) ? $data['status'] : $journal->status;
        $journal->balance_status = isset($data['status']) ? $data['status'] : $journal->balance_status;
        if ($journal->isDirty()) {
            $journal->save();
        }
        return $journal->refresh();
    }

    public function update_transaction_status(Journal $journal, $status)
    {
        $journal->status = $status;
        $journal->balance_status = $status;
        if ($journal->isDirty()) {
            $journal->save();
        }
        return $journal->fresh();
    }

    public function update_transaction_amount(Journal $journal, $amount)
    {
        $journal->amount = $amount;
        if ($journal->isDirty()) {
            $journal->save();
        }
        return $journal->refresh();
    }
    

    /**
     * @param Journal  $journal
     * @param array $data
     *
     * @return mixed
     */

    public function allocateJournal($voucher, $m_account, $c_account, $sender_customer_account)
    {
        $d['resourceable_id'] = $voucher->id;
        $d['type'] = 'Voucher';
        $d['status'] = 0;
        $branch = $voucher->sender_city->branch;
        $branch_account_id = $branch->account->id;
        $branch_delivery_commission = $branch->delivery_commission;
        $hq_account_id = Account::where('accountable_type', 'HQ')->first()->id;

        if ($voucher->total_coupon_amount > 0) {
            $delivery_amount = $voucher->total_delivery_amount - $voucher->total_coupon_amount;
        } else {
            $delivery_amount = $voucher->discount_type == "extra" ?
                $voucher->total_delivery_amount + $voucher->total_discount_amount : $voucher->total_delivery_amount - $voucher->total_discount_amount;
        }

        $total_extra_amount =  $voucher->transaction_fee + $voucher->insurance_fee + $voucher->warehousing_fee;
        
        switch ($voucher->payment_type_id) {
            // Normal Delivery
            case 1: //Sum Total
                $count=4;
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {//customer to branch account
                        $d['debit_account_id'] = $c_account->id;
                        $d['credit_account_id'] =  $voucher->receiver_city->branch->account->id;
                        $d['amount'] = $voucher->total_amount_to_collect;
                    } elseif ($i==1) {// branch to HQ
                        $d['debit_account_id'] = $branch_account_id;
                        $d['credit_account_id'] = $hq_account_id;
                        $d['amount'] = $voucher->total_amount_to_collect;
                    } elseif ($i==2) {// HQ to Branch
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $branch_delivery_commission;
                    } elseif ($i==3) {// HQ to Merchant
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = $m_account->id;
                        $d['amount'] = $voucher->total_item_price - $total_extra_amount;
                    }
                    $journal = $this->create_journal($d);
                }

                return $journal->refresh();
            break;
                            
            case 2: //Net Total
                $count=4;
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {//customer to branch account
                        $d['debit_account_id'] = $c_account->id;
                        $d['credit_account_id'] =  $branch_account_id;
                        $d['amount'] = $voucher->total_amount_to_collect;
                    } elseif ($i==1) {// branch to HQ
                        $d['debit_account_id'] = $branch_account_id;
                        $d['credit_account_id'] = $hq_account_id;
                        $d['amount'] = $voucher->total_amount_to_collect;
                    } elseif ($i==2) {// HQ to Branch
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $branch_delivery_commission;
                    } elseif ($i==3) {// HQ to Merchant
                        if(!$m_account->merchant->is_corporate_merchant){
                            $d['debit_account_id'] = $hq_account_id;
                            $d['credit_account_id'] = $m_account->id;
                            $d['amount'] = $voucher->total_amount_to_collect - ($delivery_amount + $total_extra_amount);
                        }else{
                            $d['debit_account_id'] = $hq_account_id;
                            $d['credit_account_id'] = $m_account->id;
                            $d['amount'] = $voucher->total_amount_to_collect;

                            $tempJournalRepository = new TempJournalRepository();
                            $temp_journal['merchant_id'] = $m_account->merchant->id ;
                            $temp_journal['debit_account_id'] = $m_account->id ;
                            $temp_journal['credit_account_id'] = $hq_account_id;
                            $temp_journal['amount'] = $delivery_amount + $total_extra_amount;
                            $temp_journal['resourceable_id'] = $voucher->id;
                            $temp_journal['type'] = 'Voucher';

                            $temp = $tempJournalRepository->create_temp_journal($voucher, $temp_journal);
                        }
                    }
                    $journal = $this->create_journal($d);
                }
                return $journal->refresh();
            break;

            case 3: //Delivery Only
                $count=4;
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {
                        $d['debit_account_id'] = $c_account->id;
                        $d['credit_account_id'] = $voucher->receiver_city->branch->account->id;
                        $d['amount'] = $delivery_amount;
                    } elseif ($i==1) {// branch to HQ
                        $d['debit_account_id'] = $branch_account_id;
                        $d['credit_account_id'] = $hq_account_id;
                        $d['amount'] = $voucher->total_amount_to_collect;
                    } elseif ($i==2) {// HQ to Branch
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $branch_delivery_commission;
                    } elseif ($i==3) {// Merchant to HQ
                        $d['debit_account_id'] = ($m_account) ? $m_account->id : $c_account->id;;
                        $d['credit_account_id'] = $hq_account_id;
                        $d['amount'] = 0;
                    }
                    $journal = $this->create_journal($d);
                }
                return $journal->refresh();
            break;

            case 4: //NTC//Unpaid Unpaid//Paid Unpaid
                $count=2;
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {// merchant to HQ
                        $d['debit_account_id'] = $m_account->id;
                        $d['credit_account_id'] = $hq_account_id;
                        $d['amount'] = $delivery_amount + $total_extra_amount;
                    } elseif ($i==1) {// HQ to branch
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $branch_delivery_commission;
                    }
                    $journal = $this->create_journal($d);
                }
                return $journal->refresh();
            break;
            case 5://Unpaid Unpaid
                $count = 1;
                $amount = $delivery_amount;
                for ($i = 0; $i < $count; $i++) {
                    if ($i == 0) {
                        $d['debit_account_id'] = $m_account->id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $amount;
                    }
                    $journal = $this->create_journal($d);
                    // $account = $this->update_debit_credit_amount($journal);
                    // $debit_account = Account::where('id', $journal->debit_account_id)->first();
                    // $credit_account = Account::where('id', $journal->credit_account_id)->first();
                    $this->update_debit_amount($journal);
                    $this->update_credit_amount($journal);
                }
                return $journal->refresh();
            break;
            case 7: //Paid Unpaid
                $count = 1;
                $amount = $delivery_amount;
                for ($i = 0; $i < $count; $i++) {
                    if ($i == 0) {
                        $d['debit_account_id'] = ($m_account) ? $m_account->id : $sender_customer_account->id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $amount;
                    }
                    $journal = $this->create_journal($d);
                    //$account = $this->update_debit_credit_amount($journal);
                    // $debit_account = Account::where('id', $journal->debit_account_id)->first();
                    // $credit_account = Account::where('id', $journal->credit_account_id)->first();
                    //$this->update_debit_amount($journal);
                    $this->update_credit_amount($journal);
                }
                return $journal->refresh();
            break;

            case 6: //UnPaid Paid
                $count=3;
                $gate = Gate::findOrFail($voucher->sender_gate_id);
                $gate_account = $gate->account;
                if (!$gate_account) {
                    $gate_account = $this->create_account($gate, 'Gate');
                }
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {
                        $d['debit_account_id'] = $m_account->id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $delivery_amount;
                    } elseif ($i==1) {
                        $d['debit_account_id'] = $m_account->id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $voucher->total_bus_fee;
                    } elseif ($i==2) {
                        $d['debit_account_id'] = $branch_account_id;
                        $d['credit_account_id'] = $gate_account->id;
                        $d['amount'] = $voucher->total_bus_fee;
                    }
                    $journal = $this->create_journal($d);
                    // $account = $this->update_debit_credit_amount($journal);
                    // $debit_account = Account::where('id', $journal->debit_account_id)->first();
                    // $credit_account = Account::where('id', $journal->credit_account_id)->first();
                    if ($i!=1) {
                        $this->update_debit_amount($journal);
                    }
                    $this->update_credit_amount($journal);
                }
                return $journal->refresh();
            break;
            
            case 8: //Paid Paid
                $count=2;
                $gate = Gate::findOrFail($voucher->sender_gate_id);
                $gate_account = $gate->account;
                if (!$gate_account) {
                    $gate_account = $this->create_account($gate, 'Gate');
                }

                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {
                        $d['debit_account_id'] = ($m_account) ? $m_account->id : $sender_customer_account->id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $delivery_amount + $voucher->total_bus_fee;
                    } elseif ($i==1) {
                        $d['debit_account_id'] = $branch_account_id;
                        $d['credit_account_id'] = $gate_account->id;
                        $d['amount'] = $voucher->total_bus_fee;
                    }
                    $journal = $this->create_journal($d);
                    // $account = $this->update_debit_credit_amount($journal);
                    // $debit_account = Account::where('id', $journal->debit_account_id)->first();
                    // $credit_account = Account::where('id', $journal->credit_account_id)->first();
                    if ($journal->debit_account->accountable_type != 'Merchant') {
                        $this->update_debit_amount($journal);
                    }
                    $this->update_credit_amount($journal);
                }
                return $journal->refresh();
            break;

            case 9: //Prepaid NTC
                $count=4;
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {
                        $d['debit_account_id'] = ($m_account) ? $m_account->id : $sender_customer_account->id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $delivery_amount + $total_extra_amount;
                        $d['type']   = 'Voucher';
                    } elseif ($i==1) {// HQ to branch
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $branch_delivery_commission;
                    } elseif ($i==2) {// branch to HQ
                        $d['debit_account_id'] = $branch_account_id;
                        $d['credit_account_id'] = $hq_account_id;
                        $d['amount'] = $delivery_amount + $total_extra_amount;
                    }elseif ($i==3) {// Merchant to HQ
                        $d['debit_account_id'] = ($m_account) ? $m_account->id : $sender_customer_account->id;;
                        $d['credit_account_id'] = $hq_account_id;
                        $d['amount'] =  0;
                    }
                    $journal = $this->create_journal($d);
                }
                return $journal->refresh();
            break;
            case 10: //Prepaid Collect
                $count=5;
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {
                        $d['debit_account_id'] = ($m_account) ? $m_account->id : $sender_customer_account->id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $delivery_amount + $total_extra_amount;
                    } elseif ($i==1) {
                        $d['debit_account_id'] = $c_account->id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $voucher->total_amount_to_collect;
                    } elseif ($i==2) {
                        $d['debit_account_id'] = $branch_account_id;
                        $d['credit_account_id'] = $hq_account_id;
                        $d['amount'] = $voucher->total_amount_to_collect + $delivery_amount + $total_extra_amount;
                    } elseif ($i==3) {
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $branch_delivery_commission;
                    } elseif ($i==4) {
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = ($m_account) ? $m_account->id : $sender_customer_account->id;
                        $d['amount'] = $voucher->total_amount_to_collect - $total_extra_amount;
                    }
                    
                    $journal = $this->create_journal($d);
                }
            return $journal->refresh();
            break;
        }
    }

    public function allocateBranchToBranchJournal($voucher, $m_account, $c_account, $sender_customer_account)
    {
        $d['resourceable_id'] = $voucher->id;
        $d['type'] = 'Voucher';
        $d['status'] = 0;

        $hq_account_id = getHqAccount()->id;
        
        $is_sender_branch = $voucher->sender_city->branch;
        $is_receiver_branch = $voucher->receiver_city->branch;

        if ($is_sender_branch) {
            $sender_branch_or_agent = $is_sender_branch;
        } else {
             $sender_branch_or_agent = ($voucher->from_agent) ? $voucher->from_agent : $voucher->sender_city->agent;
            // $sender_branch_or_agent = $voucher->agent;
        }

        $receiver_zone_agent_rate =0;
        if ($is_receiver_branch) {
            $receiver_branch_or_agent = $is_receiver_branch;
        } else {
            $receiver_branch_or_agent = ($voucher->to_agent) ? $voucher->to_agent : $voucher->receiver_city->agent;
            $receiver_zone_agent_rate = ($voucher->receiver_zone)? $voucher->receiver_zone->zone_agent_rate : 0;
        }
        

        $sender_branch_account_id = $sender_branch_or_agent->account->id;
        $sender_branch_delivery_commission = $sender_branch_or_agent->delivery_commission;

        $receiver_branch_account_id = $receiver_branch_or_agent->account->id;
        $receiver_branch_delivery_commission = $receiver_branch_or_agent->delivery_commission + $receiver_zone_agent_rate;
        

        if ($voucher->total_coupon_amount > 0) {
            $delivery_amount = $voucher->total_delivery_amount - $voucher->total_coupon_amount;
        } else {
            $delivery_amount = $voucher->discount_type == "extra" ?
                $voucher->total_delivery_amount + $voucher->total_discount_amount : $voucher->total_delivery_amount - $voucher->total_discount_amount;
        }

        $total_extra_amount =  $voucher->transaction_fee + $voucher->insurance_fee + $voucher->warehousing_fee;
        $total_delivery_amount = $delivery_amount;
        
        switch ($voucher->payment_type_id) {
            // Normal Delivery
            case 1: //Sum Total
                $count=5;
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {//customer to branch account
                        $d['debit_account_id'] = $c_account->id;
                        $d['credit_account_id'] =  $receiver_branch_account_id;
                        $d['amount'] = $voucher->total_amount_to_collect;
                    } elseif ($i==1) {// Receiver branch to HQ
                        $d['debit_account_id'] = $receiver_branch_account_id;
                        $d['credit_account_id'] = $hq_account_id;
                        $d['amount'] = $voucher->total_amount_to_collect;
                    } elseif ($i==2) {// HQ to Receiver Branch
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = $receiver_branch_account_id;
                        $d['amount'] = $receiver_branch_delivery_commission;
                    } elseif ($i==3) {// HQ to Merchant
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = $m_account->id;
                        $d['amount'] = $voucher->total_item_price  - $total_extra_amount;
                    } elseif ($i==4) {// HQ to Sender Branch
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = $sender_branch_account_id;
                        $d['amount'] = $sender_branch_delivery_commission;
                    }
                    $journal = $this->create_journal($d);
                }
                return $journal;
            break;
                            
            case 2: //Net Total
                $count=5;
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {//customer to branch account
                        $d['debit_account_id'] = $c_account->id;
                        $d['credit_account_id'] =  $receiver_branch_account_id;
                        $d['amount'] = $voucher->total_item_price;
                    } elseif ($i==1) {// Receiver branch to HQ
                        $d['debit_account_id'] = $receiver_branch_account_id;
                        $d['credit_account_id'] = $hq_account_id;
                        $d['amount'] = $voucher->total_item_price;
                    } elseif ($i==2) {// HQ to Receiver Branch
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = $receiver_branch_account_id;
                        $d['amount'] = $receiver_branch_delivery_commission;
                    } elseif ($i==3) {// HQ to Merchant
                        if(!$m_account->merchant->is_corporate_merchant){
                            $d['debit_account_id'] = $hq_account_id;
                            $d['credit_account_id'] = $m_account->id;
                            $d['amount'] = $voucher->total_amount_to_collect - ($total_delivery_amount + $total_extra_amount);
                        
                        }else{
                            $d['debit_account_id'] = $hq_account_id;
                            $d['credit_account_id'] = $m_account->id;
                            $d['amount'] = $voucher->total_amount_to_collect;

                            $tempJournalRepository = new TempJournalRepository();
                            $temp_journal['merchant_id'] = $m_account->merchant->id ;
                            $temp_journal['debit_account_id'] = $m_account->id ;
                            $temp_journal['credit_account_id'] = $hq_account_id;
                            $temp_journal['amount'] = $delivery_amount + $total_extra_amount;
                            $temp_journal['resourceable_id'] = $voucher->id;
                            $temp_journal['type'] = 'Voucher';

                            $temp = $tempJournalRepository->create_temp_journal($voucher, $temp_journal);
                        }
                    } elseif ($i==4) {// HQ to Sender Branch
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = $sender_branch_account_id;
                        $d['amount'] = $sender_branch_delivery_commission;
                    }
                    $journal = $this->create_journal($d);
                }

                return $journal;
            break;

            case 3: //Delivery Only
                $count=5;
                
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {
                        $d['debit_account_id'] = $c_account->id;
                        $d['credit_account_id'] = $receiver_branch_account_id;
                        $d['amount'] = $total_delivery_amount;
                    } elseif ($i==1) {// branch to HQ
                        $d['debit_account_id'] = $receiver_branch_account_id;
                        $d['credit_account_id'] = $hq_account_id;
                        $d['amount'] = $total_delivery_amount;
                    } elseif ($i==2) {// HQ to Branch
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = $receiver_branch_account_id;
                        $d['amount'] = $receiver_branch_delivery_commission;
                    } elseif ($i==3) {// HQ to Branch
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = $sender_branch_account_id;
                        $d['amount'] = $sender_branch_delivery_commission;
                    }elseif ($i==4) {// Merchant to HQ
                        $d['debit_account_id'] = ($m_account) ? $m_account->id :$c_account->id;
                        $d['credit_account_id'] = $hq_account_id;
                        $d['amount'] = 0;
                    }
                    
                    $journal = $this->create_journal($d);
                    
                }
                return $journal;
            break;

            case 4: //NTC//Unpaid Unpaid//Paid Unpaid
                $count=3;
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {// merchant to HQ
                        $d['debit_account_id'] = $m_account->id;
                        $d['credit_account_id'] = $hq_account_id;
                        $d['amount'] = $total_delivery_amount;
                    } elseif ($i==1) {// HQ to branch
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = $receiver_branch_account_id;
                        $d['amount'] = $receiver_branch_delivery_commission;
                    } elseif ($i==2) {// HQ to branch
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = $sender_branch_account_id;
                        $d['amount'] = $sender_branch_delivery_commission;
                    }
                    $journal = $this->create_journal($d);
                }
                return $journal;
            break;
            case 5://Unpaid Unpaid
                $count = 1;
                $amount = $delivery_amount;
                for ($i = 0; $i < $count; $i++) {
                    if ($i == 0) {
                        $d['debit_account_id'] = $m_account->id;
                        $d['credit_account_id'] = $receiver_branch_account_id;
                        $d['amount'] = $amount;
                    }
                    $journal = $this->create_journal($d);
                    // $account = $this->update_debit_credit_amount($journal);
                    // $debit_account = Account::where('id', $journal->debit_account_id)->first();
                    // $credit_account = Account::where('id', $journal->credit_account_id)->first();
                    $this->update_debit_amount($journal);
                    $this->update_credit_amount($journal);
                }
                return $journal;
            break;
            case 7: //Paid Unpaid
                $count = 1;
                $amount = $delivery_amount;
                for ($i = 0; $i < $count; $i++) {
                    if ($i == 0) {
                        $d['debit_account_id'] = ($m_account) ? $m_account->id : $sender_customer_account->id;
                        $d['credit_account_id'] = $receiver_branch_account_id;
                        $d['amount'] = $amount;
                    }
                    $journal = $this->create_journal($d);
                    //$account = $this->update_debit_credit_amount($journal);
                    // $debit_account = Account::where('id', $journal->debit_account_id)->first();
                    // $credit_account = Account::where('id', $journal->credit_account_id)->first();
                    //$this->update_debit_amount($journal);
                    $this->update_credit_amount($journal);
                }
                return $journal;
            break;

            case 6: //UnPaid Paid
                $count=3;
                $gate = Gate::findOrFail($voucher->sender_gate_id);
                $gate_account = $gate->account;
                if (!$gate_account) {
                    $gate_account = $this->create_account($gate, 'Gate');
                }
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {
                        $d['debit_account_id'] = $m_account->id;
                        $d['credit_account_id'] = $receiver_branch_account_id;
                        $d['amount'] = $delivery_amount;
                    } elseif ($i==1) {
                        $d['debit_account_id'] = $m_account->id;
                        $d['credit_account_id'] = $receiver_branch_account_id;
                        $d['amount'] = $voucher->total_bus_fee;
                    } elseif ($i==2) {
                        $d['debit_account_id'] = $receiver_branch_account_id;
                        $d['credit_account_id'] = $gate_account->id;
                        $d['amount'] = $voucher->total_bus_fee;
                    }
                    $journal = $this->create_journal($d);
                    // $account = $this->update_debit_credit_amount($journal);
                    // $debit_account = Account::where('id', $journal->debit_account_id)->first();
                    // $credit_account = Account::where('id', $journal->credit_account_id)->first();
                    if ($i!=1) {
                        $this->update_debit_amount($journal);
                    }
                    $this->update_credit_amount($journal);
                }
                return $journal;
            break;
            
            case 8: //Paid Paid
                $count=2;
                $gate = Gate::findOrFail($voucher->sender_gate_id);
                $gate_account = $gate->account;
                if (!$gate_account) {
                    $gate_account = $this->create_account($gate, 'Gate');
                }

                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {
                        $d['debit_account_id'] = ($m_account) ? $m_account->id : $sender_customer_account->id;
                        $d['credit_account_id'] = $receiver_branch_account_id;
                        $d['amount'] = $delivery_amount + $voucher->total_bus_fee;
                    } elseif ($i==1) {
                        $d['debit_account_id'] = $receiver_branch_account_id;
                        $d['credit_account_id'] = $gate_account->id;
                        $d['amount'] = $voucher->total_bus_fee;
                    }
                    $journal = $this->create_journal($d);
                    // $account = $this->update_debit_credit_amount($journal);
                    // $debit_account = Account::where('id', $journal->debit_account_id)->first();
                    // $credit_account = Account::where('id', $journal->credit_account_id)->first();
                    if ($journal->debit_account->accountable_type != 'Merchant') {
                        $this->update_debit_amount($journal);
                    }
                    $this->update_credit_amount($journal);
                }
                return $journal;
            break;

            case 9: //Prepaid NTC
                $count=5;
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {
                        $d['debit_account_id'] = ($m_account) ? $m_account->id : $sender_customer_account->id;
                        $d['credit_account_id'] = $sender_branch_account_id;
                        $d['amount'] = $total_delivery_amount;
                        $d['type']   = 'Voucher';
                    } elseif ($i==1) {// HQ to branch
                        $d['debit_account_id'] = $sender_branch_account_id;
                        $d['credit_account_id'] = $hq_account_id;
                        $d['amount'] = $total_delivery_amount;
                    } elseif ($i==2) {// branch to HQ
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = $sender_branch_account_id;
                        $d['amount'] = $sender_branch_delivery_commission;
                    } elseif ($i==3) {// branch to HQ
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = $receiver_branch_account_id;
                        $d['amount'] = $receiver_branch_delivery_commission;
                    }elseif ($i==4) {// branch to HQ
                        $d['debit_account_id'] = ($m_account) ? $m_account->id : $sender_customer_account->id;
                        $d['credit_account_id'] = $hq_account_id;
                        $d['amount'] = 0;
                    }
                    $journal = $this->create_journal($d);
                }
                return $journal;
            break;
            case 10: //Prepaid Collect
                $count=7;
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {
                        $d['debit_account_id'] = ($m_account) ? $m_account->id : $sender_customer_account->id;
                        $d['credit_account_id'] = $sender_branch_account_id;
                        $d['amount'] = $total_delivery_amount;
                    } elseif ($i==1) {
                        $d['debit_account_id'] = $c_account->id;
                        $d['credit_account_id'] = $receiver_branch_account_id;
                        $d['amount'] = $voucher->total_amount_to_collect;
                    } elseif ($i==2) {
                        $d['debit_account_id'] = $sender_branch_account_id;
                        $d['credit_account_id'] = $hq_account_id;
                        $d['amount'] = $total_delivery_amount;
                    } elseif ($i==3) {
                        $d['debit_account_id'] = $receiver_branch_account_id;
                        $d['credit_account_id'] = $hq_account_id;
                        $d['amount'] = $voucher->total_amount_to_collect;
                    } elseif ($i==4) {
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = $sender_branch_account_id;
                        $d['amount'] = $sender_branch_delivery_commission;
                    } elseif ($i==5) {
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = $receiver_branch_account_id;
                        $d['amount'] = $receiver_branch_delivery_commission;
                    } elseif ($i==6) {
                        $d['debit_account_id'] = $hq_account_id;
                        $d['credit_account_id'] = ($m_account) ? $m_account->id : $sender_customer_account->id;
                        $d['amount'] = $voucher->total_amount_to_collect - $total_extra_amount;
                    }
                    
                    $journal = $this->create_journal($d);
                }
            return $journal;
            break;
        }
    }

    public function create_journal($data)
    {
        
        $journal = Journal::create([
            //'journal_no' => 'J' . str_pad($journal_id, 6, '0', STR_PAD_LEFT),
            'debit_account_id' => $data['debit_account_id'],
            'credit_account_id' => $data['credit_account_id'],
            'resourceable_type' => $data['type'],
            'resourceable_id' => $data['resourceable_id'],
            'amount' => $data['amount'],
            'status' => $data['status'],
            'balance_status' => $data['status'],
            'created_by' => auth()->user() ? auth()->user()->id : 1
        ]);
        
        return $journal->fresh();
    }

    public function JournalCreateData($debit_account, $credit_account, $amount, $resource, $type, $status=0)
    {
        $journal['debit_account_id'] = $debit_account;
        $journal['credit_account_id'] = $credit_account;
        $journal['amount'] = $amount;
        $journal['type'] = $type;
        $journal['resourceable_id'] = $resource->id;
        $journal['status'] = $status;
        $journal['balance_status'] = $status;

        $journal = $this->create_journal($journal);

        // $this->update_deibt_amount($journal);
        // $this->update_credit_amount($journal);
    }

    public function update_debit_amount($journal)
    {
        //calculate debit and balance
        $debit_account = $journal->debit_account;
        $debit_account->increment('debit', $journal->amount);
        $debit_balance_amount = $debit_account->credit - $debit_account->debit;
        $debit_account->update(['balance' => $debit_balance_amount]);
    }

    public function update_credit_amount($journal)
    {
        //calculate credit and balance
        $credit_account = $journal->credit_account;
        $credit_account->increment('credit', $journal->amount);
        $credit_balance_amount = $credit_account->credit - $credit_account->debit;
        $credit_account->update(['balance' => $credit_balance_amount]);
    }

    public function create_account($data, $type)
    {
        $accountRepository = new AccountRepository();
        if ($type == 'Gate') {
            $city_id = $data->bus_station->city_id;
        } elseif ($type == 'Staff') {
            $city_id = isset($data->zone) ? $data->zone->city_id : 64;
        } elseif ($type == 'Zone' || $type == 'Customer' || $type == 'Agent') {
            $city_id = $data->city_id;
        } elseif ($type == 'Merchant') {
            $city_id = $data->merchant_associates[0]->city_id;
        }
        $account = [
            'city_id' => $city_id,
            'accountable_type' => $type,
            'accountable_id' => $data->id,
        ];
        $acc = $accountRepository->create($account);
        return $acc;
    }

    /**
     * @param Journal  $journal
     * @param array $data
     *
     * @return mixed
     */

    public function allocateJournalbackup($voucher, $m_account, $c_account, $sender_customer_account)
    {
        $d['resourceable_id'] = $voucher->id;
        $d['type'] = 'Voucher';
        $d['status'] = 0;
        $branch_account_id = $voucher->sender_city->branch->account->id;

        if ($voucher->total_coupon_amount > 0) {
            $delivery_amount = $voucher->total_delivery_amount - $voucher->total_coupon_amount;
        } else {
            $delivery_amount = $voucher->discount_type == "extra" ?
                $voucher->total_delivery_amount + $voucher->total_discount_amount : $voucher->total_delivery_amount - $voucher->total_discount_amount;
        }

        $total_extra_amount =  $voucher->transaction_fee + $voucher->insurance_fee + $voucher->warehousing_fee;
        
        switch ($voucher->payment_type_id) {
            // Normal Delivery
            case 1: //Sum Total
                $count=3;
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {
                        $d['debit_account_id'] = $m_account->id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $delivery_amount + $total_extra_amount;
                    } elseif ($i==1) {
                        $d['debit_account_id'] = $branch_account_id;
                        $d['credit_account_id'] = $m_account->id;
                        $d['amount'] = $voucher->total_item_price - $total_extra_amount;
                    } elseif ($i==2) {
                        $d['debit_account_id'] = $c_account->id;
                        $d['credit_account_id'] =  $voucher->receiver_city->branch->account->id;
                        $d['amount'] = $voucher->total_amount_to_collect;
                    }
                    $journal = $this->create_journal($d);
                    // $debit_account = Account::where('id', $journal->debit_account_id)->first();
                    // $credit_account = Account::where('id', $journal->credit_account_id)->first();
    
                    if ($journal->debit_account->accountable_type != 'Merchant') {
                        $this->update_debit_amount($journal);
                    }
                    if ($journal->debit_account->accountable_type == 'Customer' || $journal->debit_account->accountable_type == 'Zone') {
                        $this->update_credit_amount($journal);
                    }
                }
                return $journal->fresh();
            break;
                            
            case 2: //Net Total
                $count=3;
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {
                        $d['debit_account_id'] = $m_account->id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $delivery_amount + $total_extra_amount;
                    } elseif ($i==1) {
                        $d['debit_account_id'] = $branch_account_id;
                        $d['credit_account_id'] = $m_account->id;
                        $d['amount'] = $voucher->total_item_price;
                    } elseif ($i==2) {
                        $d['debit_account_id'] = $c_account->id;
                        $d['credit_account_id'] = $voucher->receiver_city->branch->account->id;
                        $d['amount'] = $voucher->total_item_price;
                    }
                    $journal = $this->create_journal($d);
                    //$account = $this->update_debit_credit_amount($journal);
                    // $debit_account = Account::where('id', $journal->debit_account_id)->first();
                    // $credit_account = Account::where('id', $journal->credit_account_id)->first();
                    $this->update_debit_amount($journal);
                    $this->update_credit_amount($journal);
                }
                return $journal->fresh();
            break;

            case 3: //Delivery Only
                $count=1;
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {
                        $d['debit_account_id'] = $c_account->id;
                        $d['credit_account_id'] = $voucher->receiver_city->branch->account->id;
                        $d['amount'] = $delivery_amount;
                    }
                    $journal = $this->create_journal($d);
                    //$account = $this->update_debit_credit_amount($journal);
                    // $debit_account = Account::where('id', $journal->debit_account_id)->first();
                    // $credit_account = Account::where('id', $journal->credit_account_id)->first();
                    $this->update_debit_amount($journal);
                    $this->update_credit_amount($journal);
                }
                return $journal->fresh();
            break;

            case 4: //NTC//Unpaid Unpaid//Paid Unpaid
                $count=1;
                $amount = $delivery_amount;
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {
                        $d['debit_account_id'] = $m_account->id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $amount + $total_extra_amount;
                    }
                    $journal = $this->create_journal($d);
                    // $account = $this->update_debit_credit_amount($journal);
                    // $debit_account = Account::where('id', $journal->debit_account_id)->first();
                    // $credit_account = Account::where('id', $journal->credit_account_id)->first();
                    $this->update_debit_amount($journal);
                    $this->update_credit_amount($journal);
                }
                return $journal->fresh();
            break;
            case 5://Unpaid Unpaid
                $count = 1;
                $amount = $delivery_amount;
                for ($i = 0; $i < $count; $i++) {
                    if ($i == 0) {
                        $d['debit_account_id'] = $m_account->id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $amount;
                    }
                    $journal = $this->create_journal($d);
                    // $account = $this->update_debit_credit_amount($journal);
                    // $debit_account = Account::where('id', $journal->debit_account_id)->first();
                    // $credit_account = Account::where('id', $journal->credit_account_id)->first();
                    $this->update_debit_amount($journal);
                    $this->update_credit_amount($journal);
                }
                return $journal->fresh();
            break;
            case 7: //Paid Unpaid
                $count = 1;
                $amount = $delivery_amount;
                for ($i = 0; $i < $count; $i++) {
                    if ($i == 0) {
                        $d['debit_account_id'] = ($m_account) ? $m_account->id : $sender_customer_account->id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $amount;
                    }
                    $journal = $this->create_journal($d);
                    //$account = $this->update_debit_credit_amount($journal);
                    // $debit_account = Account::where('id', $journal->debit_account_id)->first();
                    // $credit_account = Account::where('id', $journal->credit_account_id)->first();
                    //$this->update_debit_amount($journal);
                    $this->update_credit_amount($journal);
                }
                return $journal->fresh();
            break;

            case 6: //UnPaid Paid
                $count=3;
                $gate = Gate::findOrFail($voucher->sender_gate_id);
                $gate_account = $gate->account;
                if (!$gate_account) {
                    $gate_account = $this->create_account($gate, 'Gate');
                }
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {
                        $d['debit_account_id'] = $m_account->id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $delivery_amount;
                    } elseif ($i==1) {
                        $d['debit_account_id'] = $m_account->id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $voucher->total_bus_fee;
                    } elseif ($i==2) {
                        $d['debit_account_id'] = $branch_account_id;
                        $d['credit_account_id'] = $gate_account->id;
                        $d['amount'] = $voucher->total_bus_fee;
                    }
                    $journal = $this->create_journal($d);
                    // $account = $this->update_debit_credit_amount($journal);
                    // $debit_account = Account::where('id', $journal->debit_account_id)->first();
                    // $credit_account = Account::where('id', $journal->credit_account_id)->first();
                    if ($i!=1) {
                        $this->update_debit_amount($journal);
                    }
                    $this->update_credit_amount($journal);
                }
                return $journal->fresh();
            break;
            
            case 8: //Paid Paid
                $count=2;
                $gate = Gate::findOrFail($voucher->sender_gate_id);
                $gate_account = $gate->account;
                if (!$gate_account) {
                    $gate_account = $this->create_account($gate, 'Gate');
                }

                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {
                        $d['debit_account_id'] = ($m_account) ? $m_account->id : $sender_customer_account->id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $delivery_amount + $voucher->total_bus_fee;
                    } elseif ($i==1) {
                        $d['debit_account_id'] = $branch_account_id;
                        $d['credit_account_id'] = $gate_account->id;
                        $d['amount'] = $voucher->total_bus_fee;
                    }
                    $journal = $this->create_journal($d);
                    // $account = $this->update_debit_credit_amount($journal);
                    // $debit_account = Account::where('id', $journal->debit_account_id)->first();
                    // $credit_account = Account::where('id', $journal->credit_account_id)->first();
                    if ($journal->debit_account->accountable_type != 'Merchant') {
                        $this->update_debit_amount($journal);
                    }
                    $this->update_credit_amount($journal);
                }
                return $journal->fresh();
            break;

            case 9: //Prepaid NTC
                $count=1;
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {
                        $d['debit_account_id'] = ($m_account) ? $m_account->id : $sender_customer_account->id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $delivery_amount + $total_extra_amount;
                        $d['type']   = 'Voucher';
                    }
                    $journal = $this->create_journal($d);
                    //$account = $this->update_debit_credit_amount($journal, 9);
                    // $debit_account = Account::where('id', $journal->debit_account_id)->first();
                    // $credit_account = Account::where('id', $journal->credit_account_id)->first();
                    if ($journal->debit_account->accountable_type != 'Merchant') {
                        $this->update_debit_amount($journal);
                    }
                    $this->update_credit_amount($journal);
                }
                return $journal->fresh();
            break;
            case 10: //Prepaid Collect
                $count=3;
                for ($i=0; $i < $count; $i++) {
                    if ($i==0) {
                        $d['debit_account_id'] = ($m_account) ? $m_account->id : $sender_customer_account->id;
                        $d['credit_account_id'] = $branch_account_id;
                        $d['amount'] = $delivery_amount;
                    } elseif ($i==1) {
                        $d['debit_account_id'] = $branch_account_id;
                        $d['credit_account_id'] = ($m_account) ? $m_account->id : $sender_customer_account->id;
                        $d['amount'] = $voucher->total_amount_to_collect - $total_extra_amount;//$voucher->total_item_price;
                    } elseif ($i==2) {
                        $d['debit_account_id'] = $c_account->id;
                        $d['credit_account_id'] = $voucher->receiver_city->branch->account->id;//$branch_account_id;
                        $d['amount'] = $voucher->total_amount_to_collect;
                    }
                    
                    $journal = $this->create_journal($d);
                    // $debit_account = Account::where('id', $journal->debit_account_id)->first();
                    // $credit_account = Account::where('id', $journal->credit_account_id)->first();
                    if ($journal->debit_account->accountable_type != 'Merchant') {
                        $this->update_debit_amount($journal);
                    }
                    $this->update_credit_amount($journal);
                }
            return $journal->fresh();
            break;
        }
    }
}
