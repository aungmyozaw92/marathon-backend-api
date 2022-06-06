<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Gate;
use App\Models\Agent;
use App\Models\Account;
use App\Models\Voucher;
use App\Models\Waybill;
use App\Jobs\AgentRewardJob;
use App\Models\WaybillVoucher;
use App\Models\Staff;
use Illuminate\Support\Facades\Log;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\AccountRepository;
use App\Repositories\Web\Api\v1\JournalRepository;
use App\Repositories\Web\Api\v1\CustomerRepository;
use App\Repositories\Web\Api\v1\MerchantRepository;
use App\Contracts\MembershipContract;

class WaybillRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Waybill::class;
    }

    /**
     * @param array $data
     *
     * @return Waybill
     */
    protected $membershipContract;
    public function __construct(MembershipContract $membershipContract)
    {
        $this->membershipContract = $membershipContract;
    }
    public function create(array $data): Waybill
    {
        if (isset($data['note'])) {
            $note = getConvertedString($data['note']);
        }
        $waybill =  Waybill::create([
            'qty'                 => isset($data['vouchers_qty']) ? $data['vouchers_qty'] : 0,
            // 'from_city_id'        => $data['from_city_id'],
            'from_city_id'        => isset($data['from_city_id']) ? $data['from_city_id'] : getBranchCityId(), //get from meta
            'to_city_id'          => $data['to_city_id'],
            'from_bus_station_id' => $data['from_bus_station_id'],
            'to_bus_station_id'   => $data['to_bus_station_id'],
            'gate_id'             => $data['gate_id'],
            // 'city_id'             => $data['city_id'],
            'delivery_id'         => $data['delivery_id'],
            'staff_id'            => isset($data['staff_id']) ? $data['staff_id'] : auth()->user()->id,
            'note'                => isset($data['note']) ? getConvertedString($data['note']) : null,
            'created_by'          => auth()->user()->id,
            'courier_type_id'  => isset($data['courier_type_id']) ? $data['courier_type_id'] : null,
            'is_commissionable'  => isset($data['is_commissionable']) ? $data['is_commissionable'] : 0,
            'is_pointable'  => isset($data['is_pointable']) ? $data['is_pointable'] : 0,
            'from_agent_id'  => isset($data['from_agent_id']) ? $data['from_agent_id'] : null,
            'to_agent_id'  => isset($data['to_agent_id']) ? $data['to_agent_id'] : null,
        ]);

        // $waybill->vouchers()->syncWithoutDetaching($data['voucher_id']);

        if (isset($data['vouchers'])) {
            foreach ($data['vouchers'] as $voucher) {
                $note = getConvertedString($voucher['waybill_voucher_note']);

                $waybill->vouchers()->attach($voucher['id'], [
                    'note' => $note,
                    'priority' => $voucher['waybill_voucher_priority']
                ]);
                $voucher = Voucher::findOrFail($voucher['id']);
                if ($voucher->delivery_status_id == 9) {
                    $voucher->return_from_waybill = 1;
                } else {
                    $voucher->outgoing_status = 1;
                    $voucher->delivery_status_id = 2;
                    $voucher->to_agent_id = isset($data['to_agent_id']) ? $data['to_agent_id'] : $voucher->to_agent_id;
                }
                $voucher->save();
                $voucher->voucherSheetFire($waybill->waybill_invoice, 'new_waybill_voucher');
            }
        }

        // foreach ($data['voucher_id'] as $voucher) {
        //     $voucher = Voucher::findOrFail($voucher);
        //     $voucher->outgoing_status = 1;
        //     $voucher->delivery_status_id = 2;
        //     $voucher->save();
        // }

        return $waybill->refresh();
    }

    /**
     * @param Waybill  $waybill
     * @param array $data
     *
     * @return mixed
     */
    public function update(Waybill $waybill, array $data): Waybill
    {
        if (isset($data['note'])) {
            $note = getConvertedString($data['note']);
        }

        $waybill->actual_bus_fee      =  isset($data['actual_bus_fee']) ? $data['actual_bus_fee'] : $waybill->actual_bus_fee;
        $waybill->from_city_id        =  isset($data['from_city_id']) ? $data['from_city_id'] : $waybill->from_city_id;
        $waybill->to_city_id          =  isset($data['to_city_id']) ? $data['to_city_id'] : $waybill->to_city_id;
        $waybill->from_bus_station_id =  isset($data['from_bus_station_id']) ? $data['from_bus_station_id'] : $waybill->from_bus_station_id;
        $waybill->to_bus_station_id   =  isset($data['to_bus_station_id']) ? $data['to_bus_station_id'] : $waybill->to_bus_station_id;
        $waybill->gate_id             =  isset($data['gate_id']) ? $data['gate_id'] : $waybill->gate_id;
        $waybill->delivery_id         =  isset($data['delivery_id']) ? $data['delivery_id'] : $waybill->delivery_id;
        $waybill->staff_id            =  isset($data['staff_id']) ? $data['staff_id'] : $waybill->staff_id;
        $waybill->note                =  isset($data['note']) ? $note : $waybill->note;
        $waybill->courier_type_id = isset($data['courier_type_id']) ? $data['courier_type_id'] : $waybill->courier_type_id;
        $waybill->is_commissionable = isset($data['is_commissionable']) ? $data['is_commissionable'] : $waybill->is_commissionable;
        $waybill->is_pointable = isset($data['is_pointable']) ? $data['is_pointable'] : $waybill->is_pointable;
        $waybill->from_agent_id = isset($data['from_agent_id']) ? $data['from_agent_id'] : null;
        $waybill->to_agent_id = isset($data['to_agent_id']) ? $data['to_agent_id'] : null;


        if ($data['is_closed']) {
            $waybill->is_closed = 1;
            $waybill->is_paid = 1;
            $waybill->closed_date = date('Y-m-d H:i:s');
            if (!$waybill->is_delivered) {
                $waybill->is_delivered = 1;
                $waybill->delivered_date = date('Y-m-d H:i:s');
            }

            $journalRepository = new JournalRepository();

            $gate = Gate::find($waybill->gate_id);
            $gate_account = $gate->account;
            if (!$gate_account) {
                $gate_account = $journalRepository->create_account($gate, 'Gate');
            }

            $is_branch = $waybill->from_city->branch;

            if ($is_branch) {
                $authBranchAccountId = $waybill->from_city->branch->account->id;
            } else {
                // $authBranchAccountId = $waybill->from_city->agent->account->id;
                $authBranchAccountId = (optional($waybill->agent)->account) ? $waybill->agent->account->id : $waybill->from_city->agent->account->id;
            }

            // if ($data['voucher_id']) {
            //     foreach ($data['voucher_id'] as $voucherId) {
            //         $voucher = Voucher::findOrFail($voucherId);
            //         // Creating for account and journal for agent
            //         $agent_base_rate  =  $voucher->total_agent_fee;

            //         if ($voucher->isDirty()) {
            //             $voucher->updated_by = auth()->user()->id;
            //             $voucher->save();
            //         }
            //     }
            // }


            $journalRepository->JournalCreateData($authBranchAccountId, $gate_account->id, $data['actual_bus_fee'], $waybill, 'WayBill', 1);
            $hero = Staff::findOrFail($waybill->delivery_id);
            $alreadyEarned = $this->membershipContract->checkCommission($hero, $waybill);
            if ($hero && $waybill->getQtyAttribute() > 0 && $waybill->is_commissionable && isHero($waybill) && $alreadyEarned < 1) {
                $waybill->commission_amount = isFreelancer($waybill->delivery) ? $waybill->from_bus_station->zone->outsource_rate
                    : (isFreelancerCar($waybill->delivery) ? $waybill->from_bus_station->zone->outsource_car_rate
                        : $waybill->from_bus_station->zone->zone_commission);
                $hero_account = $hero->account ? $hero->account : $journalRepository->create_account($hero, 'Staff');
                $journalRepository->JournalCreateData($authBranchAccountId, $hero_account->id, $waybill->commission_amount, $waybill, 'WayBill', 1);
                $this->membershipContract->loggingCommission($waybill->delivery, $waybill, $waybill->from_bus_station->zone, $waybill->getQtyAttribute());
            }
            if (
                $hero && $waybill->getQtyAttribute() > 0 && $waybill->is_pointable && isHero($waybill)
                && !isBlackList($waybill->delivery) && !isFreelancerCar($waybill->delivery)
            ) {
                $this->membershipContract->earnPointPerSheet($waybill);
            }
            $gate_account->balance += $waybill->actual_bus_fee;
            $gate_account->save();
        }

        if ($waybill->isDirty()) {
            $waybill->updated_by = auth()->user()->id;
            $waybill->save();
        }

        return $waybill->refresh();
    }

    /**
     * @param Waybill $waybill
     */
    public function destroy(Waybill $waybill)
    {
        //$deleted = $this->deleteById($waybill->id);
        $deleted = $waybill->delete();
        if ($deleted) {
            $waybill->deleted_by = auth()->user()->id;
            $waybill->save();
        }
    }

    public function agent_confirm(Waybill $waybill, array $data)
    {
        // Log::info("Agent Waybill Confirm Voucher Id Array Payload");
        // Log::info($data);

        $voucher = Voucher::find($data['voucher_id'])->fresh();
        if (!$voucher->is_closed) {
            if ($data['delivery_status_id'] == 9 || $voucher->delivery_status_id == 9) {
                $voucherRepository = new VoucherRepository();
                $voucher = $voucherRepository->return($voucher);
            } else {
                // closed voucher
                if (!$voucher->is_closed && ($data['delivery_status_id'] != 10
                    || $data['delivery_status_id'] != 9)) {
                    $voucherRepository = new VoucherRepository();
                    $voucher = $voucherRepository->closed($voucher);
                }
                //record delivery count
                if ($data['delivery_status_id'] == 10) {
                    if ($voucher->delivery_counter == 1) {
                        $voucher->delivery_status_id = 2;
                    } elseif ($voucher->delivery_counter == 2) {
                        $voucher->delivery_status_id = 3;
                    } else {
                        $voucher->delivery_status_id = 3;
                    }
                }
                // counter record for return or delivered
                $voucher->delivery_counter += 1;

                if ($data['delivery_status_id'] != 10) {
                    $voucher->delivery_status_id = $data['delivery_status_id'];
                    //$voucher->deli_payment_status = 1;
                }

                if ($data['delivery_status_id'] == 10) {
                    $voucher->outgoing_status = null;
                } elseif ($data['delivery_status_id'] == 8) {
                    $voucher->deli_payment_status = 1;
                    $voucher->store_status_id = 7;
                    $voucher->delivered_date = ($voucher->delivered_date) ? $voucher->delivered_date : date('Y-m-d H:i:s');
                    $voucher->transaction_date = date('Y-m-d H:i:s');
                }

                if ($voucher->isDirty()) {
                    $voucher->updated_by = auth()->user()->id;
                    $voucher->save();
                }
                $voucher->fresh();

                if ($data['delivery_status_id'] == 8 || $voucher->delivery_status_id == 8) {

                    $accountRepository = new AccountRepository();
                    $accountRepository->confirm_branch_voucher($voucher);

                    $customerRepository = new CustomerRepository();
                    $customerRepository->rate($voucher->receiver, 'success');

                    if ($voucher->platform === 'Merchant App' || $voucher->platform === 'Merchant Dashboard') {
                        $merchantRepository = new MerchantRepository();
                        $merchantRepository->calculate_reward($voucher);
                    }
                    $agent = $voucher->receiver_city->agent;
                    if ($agent) {
                        if ($agent->agent_badge && $voucher->receiver_amount_to_collect > 0 && $agent->agent_badge->id > 1) {
                            dispatch(new AgentRewardJob($agent, $voucher->receiver_amount_to_collect));
                        }
                    }
                }
            }
        }
        return $waybill->fresh();
    }
    /**
     * @param Waybill  $waybill
     * @param array $data
     *
     * @return mixed
     */
    public function remove_vouchers(Waybill $waybill, array $data): Waybill
    {
        if (isset($data['vouchers'])) {
            foreach ($data['vouchers'] as $voucherId) {
                $voucher = Voucher::findOrFail($voucherId['id']);

                $waybillVoucher = WaybillVoucher::where('waybill_id', $waybill->id)
                    ->where('voucher_id', $voucher->id)
                    ->firstOrFail();

                $deleted = $waybill->vouchers()->detach($voucherId['id']);

                if ($deleted) {
                    $voucher->outgoing_status = null;
                    $voucher->store_status_id = 4;
                    //$voucher->delivery_counter -= 1;
                }

                if ($voucher->isDirty()) {
                    $voucher->updated_by = auth()->user()->id;
                    $voucher->save();
                    $voucher->voucherSheetFire($waybill->waybill_invoice, 'remove_waybill_voucher');
                    $waybill->waybillVoucherFire($voucher->voucher_invoice, 'remove_waybill_voucher');
                }

                if ($waybill->isDirty()) {
                    $waybill->updated_by = auth()->user()->id;
                    $waybill->save();
                }
            }
        }

        return $waybill->refresh();
    }

    public function add_vouchers(Waybill $waybill, array $data): Waybill
    {
        if (isset($data['vouchers'])) {
            foreach ($data['vouchers'] as $voucher) {
                $note = null;
                if (isset($voucher['waybill_voucher_note'])) {
                    $note = getConvertedString($voucher['waybill_voucher_note']);
                }
                $waybill->vouchers()->attach($voucher['id'], [
                    'note' => $note,
                    'priority' => $voucher['waybill_voucher_priority']
                ]);
                $voucher = Voucher::findOrFail($voucher['id']);
                $voucher->outgoing_status = 1;
                if ($voucher->delivery_status_id != 9) {
                    $voucher->delivery_status_id = 2;
                }
                if ($voucher->delivery_counter < 0) {
                    $voucher->delivery_counter = 0;
                }
                $voucher->from_agent_id = $waybill->from_agent_id ? $waybill->from_agent_id : $voucher->from_agent_id;
                $voucher->to_agent_id = $waybill->to_agent_id ? $waybill->to_agent_id : $voucher->to_agent_id;
                $voucher->save();
                $voucher->voucherSheetFire($waybill->waybill_invoice, 'new_waybill_voucher');
                $waybill->waybillVoucherFire($voucher->voucher_invoice, 'new_waybill_voucher');
            }
        }

        return $waybill->refresh();
    }

    public function add_scan_vouchers(Waybill $waybill, $voucher)
    {
        $waybill->vouchers()->attach($voucher->id);
        $voucher->outgoing_status = 1;
        if ($voucher->delivery_status_id != 9) {
            $voucher->delivery_status_id = 2;
        }
        if ($voucher->store_status_id == 1) {
            $voucher->store_status_id = 2;
        }
        if ($voucher->delivery_counter < 0) {
            $voucher->delivery_counter = 0;
        }
        $voucher->to_agent_id = $waybill->to_agent_id ? $waybill->to_agent_id : $voucher->to_agent_id;
        $voucher->save();
        $voucher->voucherSheetFire($waybill->waybill_invoice, 'new_waybill_voucher');
        $waybill->waybillVoucherFire($voucher->voucher_invoice, 'new_waybill_voucher');

        return $waybill->refresh();
    }

    public function received_waybill(Waybill $waybill)
    {
        $voucherIds = $waybill->vouchers->where('delivery_status_id', '!=', 9)->pluck('id')->toArray();
        $return_voucherIds = $waybill->vouchers->where('delivery_status_id', 9)->pluck('id')->toArray();

        $waybill_return_vouchers = Voucher::whereIn('id', $return_voucherIds)->update([
            'return_from_waybill' => 2,
            'outgoing_status' => null
        ]);


        $waybill_vouchers =  Voucher::whereIn('id', $voucherIds)
            ->update([
                'origin_city_id' => $waybill->to_city_id,
                'outgoing_status' => null,
                'store_status_id' => 2
            ]);
        if ($waybill_vouchers || $waybill_return_vouchers) {
            $waybill->is_received = 1;
            $waybill->received_date = now();
            $waybill->received_by_type = 'Staff';
            $waybill->received_by_id = auth()->user()->id;
            if ($waybill->isDirty()) {
                $waybill->updated_by = auth()->user()->id;
                $waybill->save();
            }
        }
        // return true;
        return ($waybill_vouchers) ? $waybill_vouchers : $waybill_return_vouchers;
    }

    public function confirm_waybill(Waybill $waybill)
    {
        $waybill->is_confirm = 1;
        $waybill->confirmed_date = date('Y-m-d  H:i:s');
        if ($waybill->isDirty()) {
            $waybill->updated_by = auth()->user()->id;
            $waybill->save();
        }

        return $waybill->refresh();
    }

    public function agent_confirm_backup(Waybill $waybill, array $data)
    {
        //$journalRepository = new JournalRepository();
        // Log::info("Agent Waybill Confirm Voucher Id Array Payload");
        // Log::info($data);
        foreach ($data['vouchers'] as $voucherId) {

            $voucher = Voucher::findOrFail($voucherId['id'])->refresh();
            // closed voucher
            if (!$voucher->is_closed && ($voucherId['delivery_status_id'] != 10
                || $voucherId['delivery_status_id'] != 9)) {
                $voucherRepository = new VoucherRepository();
                $voucher = $voucherRepository->closed($voucher);
            }

            //$payment_id = $voucher->payment_type_id;

            //record delivery count
            if ($voucherId['delivery_status_id'] == 10) {
                if ($voucher->delivery_counter == 1) {
                    $voucher->delivery_status_id = 2;
                } elseif ($voucher->delivery_counter == 2) {
                    $voucher->delivery_status_id = 3;
                } else {
                    $voucher->delivery_status_id = 3;
                }
            }
            // counter record for return or delivered
            $voucher->delivery_counter += 1;

            if ($voucherId['delivery_status_id'] != 10) {
                $voucher->delivery_status_id = $voucherId['delivery_status_id'];
                //$voucher->deli_payment_status = 1;
            }

            if ($voucherId['delivery_status_id'] == 10) {
                $voucher->outgoing_status = null;
            } elseif ($voucherId['delivery_status_id'] == 8) {
                $voucher->deli_payment_status = 1;
                $voucher->store_status_id = 7;
                $voucher->delivered_date = ($voucher->delivered_date) ? $voucher->delivered_date : date('Y-m-d H:i:s');
                $voucher->transaction_date = date('Y-m-d H:i:s');
            }

            // if ($voucherId['delivery_status_id'] == 9 || $voucher->delivery_status_id == 9) {
            //     // $voucher->is_return = 1;
            //     $merchant_account = $voucher->pickup->sender->account;
            //     $journals = $voucher->journals->where('status', 0);
            //     foreach ($journals as $journal) {
            //         $debit_account_type = $journal->debit_account->accountable_type;
            //         $credit_account = $journal->credit_account;
            //         $credit_account_type = $credit_account->accountable_type;

            //         // if (($debit_account_type === 'Customer' || $debit_account_type === 'Branch' || $debit_account_type == 'Merchant') ||
            //         //     ($debit_account_type === 'HQ' && $credit_account_type === 'Merchant') ||
            //         //     ($debit_account_type == 'HQ' && $credit_account_type == 'Branch' && $credit_account->city_id != $voucher->sender_city_id)) {
            //         // }

            //         $journal->status = 2;
            //         $journal->balance_status = 2;
            //         if ($journal->isDirty()) {
            //             $journal->save();
            //         }

            //         if ($voucher->sender_city_id != $voucher->receiver_city_id) {
            //             if ($debit_account_type == 'HQ' && $credit_account_type == 'Branch' 
            //                 && $credit_account->city_id == $voucher->sender_city_id) 
            //                 {
            //                     $accountRepository = new AccountRepository();
            //                     $accountRepository->update_successful_balance($journal, $voucher->payment_type_id);
            //                 }
            //         }
            //     }

            //     if ($voucher->total_coupon_amount > 0) {
            //         $delivery_amount = $voucher->total_delivery_amount - $voucher->total_coupon_amount;
            //     } else {
            //         $delivery_amount = $voucher->discount_type == "extra" ?
            //             $voucher->total_delivery_amount + $voucher->total_discount_amount : $voucher->total_delivery_amount - $voucher->total_discount_amount;
            //     }
            //     $return_percentage = ($voucher->receiver_city_id != $voucher->sender_city_id) ? 100 : getReturnPercentage();
            //     $return_amount = $delivery_amount * ($return_percentage / 100);

            //     $branch_account =  $voucher->sender_city->branch->account;
            //     //$agent_account =  $voucher->receiver_city->agent->account;
            //     $hq_account =  $hq_account = Account::where('accountable_type', 'HQ')->firstOrFail();

            //     if ($payment_id == 9 || $payment_id == 10) {
            //         if ($voucher->receiver_city_id == $voucher->sender_city_id) {
            //             $journalRepository->JournalCreateData($merchant_account->id, $branch_account->id, $return_amount, $voucher, 'Voucher', 1);
            //             $journalRepository->JournalCreateData($hq_account->id, $merchant_account->id, $return_amount, $voucher, 'Voucher');
            //         } else {
            //             $journalRepository->JournalCreateData($merchant_account->id, $hq_account->id, $return_amount, $voucher, 'Voucher', 1);
            //         }

            //         // update branch and merchant balance because of prepaid type
            //         $branch_account->balance -= $delivery_amount;
            //         $branch_account->save();
            //         // if ($hq_account->isDirty()) {
            //         //     $hq_account->balance -= $delivery_amount;
            //         //     $hq_account->save();
            //         // }

            //         // if ($merchant_account->isDirty()) {
            //             // $merchant_account->balance -= $delivery_amount;
            //             // $merchant_account->save();
            //         // }
            //     } else {
            //         $journalRepository->JournalCreateData($merchant_account->id, $hq_account->id, $return_amount, $voucher, 'Voucher');
            //     }

            //     $voucher->return_fee = $return_amount;
            //     $voucher->return_type = "Noramal Return Fee";
            // }

            if ($voucher->isDirty()) {
                $voucher->updated_by = auth()->user()->id;
                $voucher->save();
            }
            $voucher->refresh();

            if ($voucherId['delivery_status_id'] == 8 || $voucher->delivery_status_id == 8) {
                $accountRepository = new AccountRepository();
                $accountRepository->confirm_branch_voucher($voucher);

                $customerRepository = new CustomerRepository();
                $customer = $customerRepository->rate($voucher->receiver, 'success');

                $voucher->end_date =  date('Y-m-d H:i:s');
            }
        }

        return $waybill->refresh();
    }
}
