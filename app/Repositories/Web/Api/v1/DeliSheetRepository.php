<?php

namespace App\Repositories\Web\Api\v1;

use Event;
use App\Models\Staff;
use App\Models\Account;
use App\Models\Journal;
use App\Models\Voucher;
use App\Models\DeliSheet;
use App\Services\SmsService;
use App\Models\DeliSheetVoucher;
use App\Repositories\BaseRepository;
use App\Contracts\MembershipContract;
use App\Repositories\Web\Api\v1\AccountRepository;
use App\Repositories\Web\Api\v1\JournalRepository;
use App\Repositories\Web\Api\v1\VoucherRepository;
use App\Repositories\Web\Api\v1\CustomerRepository;
use App\Repositories\Web\Api\v1\MerchantRepository;

class DeliSheetRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return DeliSheet::class;
    }

    /**
     * @param array $data
     *
     * @return DeliSheet
     */
    protected $membershipContract;
    public function __construct(MembershipContract $membershipContract)
    {
        $this->membershipContract = $membershipContract;
    }
    public function create(array $data): DeliSheet
    {
        $deliSheet =  DeliSheet::create([
            'qty'         => $data['qty'],
            'zone_id'     => $data['zone_id'],
            'delivery_id' => isset($data['delivery_id']) ? getConvertedString($data['delivery_id']) : null,
            'staff_id' => isset($data['staff_id']) ? getConvertedString($data['staff_id']) : null,
            'note'        => isset($data['note']) ? getConvertedString($data['note']) : null,
            'date'        => isset($data['date']) ? $data['date'] : date('Y-m-d H:i:s'),
            // 'priority'    => $data['priority'],
            'created_by'  => auth()->user()->id,
            'courier_type_id'  => isset($data['courier_type_id']) ? $data['courier_type_id'] : null,
            'is_commissionable'  => isset($data['is_commissionable']) ? $data['is_commissionable'] : 0,
            'is_pointable'  => isset($data['is_pointable']) ? $data['is_pointable'] : 0
        ]);

        // $deliSheet->vouchers()->syncWithoutDetaching($data['vouchers']);

        if (isset($data['vouchers'])) {
            foreach ($data['vouchers'] as $voucher) {
                $voucher = Voucher::findOrFail($voucher['id']);
                // $sms_service = new SmsService;
                // $customer = $voucher->receiver;
                // $sms_service->sendSmsRequest($customer->phone);
                $note = null;
                if (isset($voucher['deli_sheet_voucher_note'])) {
                    $note = getConvertedString($voucher['deli_sheet_voucher_note']);
                }

                $deliSheet->vouchers()->attach($voucher['id'], [
                    'note' => $note,
                    'priority' => $voucher['deli_sheet_voucher_priority'] ? $voucher['deli_sheet_voucher_priority'] : 0
                ]);
                $voucher = Voucher::findOrFail($voucher['id']);
                $voucher->outgoing_status = 0;
                if ($voucher->delivery_counter == 0) {
                    $voucher->delivery_status_id = 2;
                } elseif ($voucher->delivery_counter == 1) {
                    $voucher->delivery_status_id = 3;
                } elseif ($voucher->delivery_counter >= 2) {
                    $voucher->delivery_status_id = 4;
                }
                $voucher->delivery_counter += 1;
                $voucher->store_status_id = 5;
                $voucher->save();
                $voucher->voucherSheetFire($deliSheet->delisheet_invoice, 'new_delisheet_voucher');
            }
        }

        return $deliSheet->refresh();
    }

    /**
     * @param DeliSheet  $deliSheet
     * @param array $data
     *
     * @return mixed
     */
    public function update(DeliSheet $deliSheet, array $data): DeliSheet
    {
        $lunch_amount = getLunchAmount();
        // $commission_amount = getDeliveryCommission();
        // $authBranchAccountId = auth()->user()->city->branch->account->id;

        $journalRepository = new JournalRepository();

        $staff_id = isset($data['staff_id']) ? $data['staff_id'] : $deliSheet->staff_id;
        $deliSheet->qty = isset($data['qty']) ? $data['qty'] : $deliSheet->qty;
        $deliSheet->zone_id = isset($data['zone_id']) ? $data['zone_id'] : $deliSheet->zone_id;
        $deliSheet->delivery_id = isset($data['delivery_id']) ? $data['delivery_id'] : $deliSheet->delivery_id;
        $deliSheet->note = isset($data['note']) ? getConvertedString($data['note']) : $deliSheet->note;
        $deliSheet->staff_id = $staff_id;
        $deliSheet->lunch_amount = $lunch_amount;
        $deliSheet->courier_type_id = isset($data['courier_type_id']) ? $data['courier_type_id'] : $deliSheet->courier_type_id;
        $deliSheet->is_commissionable = isset($data['is_commissionable']) ? $data['is_commissionable'] : $deliSheet->is_commissionable;
        $deliSheet->is_pointable = isset($data['is_pointable']) ? $data['is_pointable'] : $deliSheet->is_pointable;
        // $deliSheet->commission_amount =  $commission_amount;
        $deliSheet->is_closed = 1;
        $deliSheet->closed_date = date('Y-m-d H:i:s');
        $collect_amount = 0;
        $delivery_commission = 0;
        if (isset($data['vouchers'])) {
            $note = null;
            foreach ($data['vouchers'] as $voucherId) {
                if (array_key_exists('note', $voucherId)) {
                    $note_string = implode(',', $voucherId['note']);
                    $separated_note = str_replace(',', '|', $note_string);
                    $note = getConvertedString($separated_note);
                }
                $voucher = Voucher::findOrFail($voucherId['id']);
                // closed voucher
                if (!$voucher->is_closed && $voucherId['delivery_status_id'] != 10) {
                    $voucherRepository = new VoucherRepository();
                    $voucher = $voucherRepository->closed($voucher);
                }

                $payment_id = $voucher->payment_type_id;

                //record delivery count
                if ($voucherId['delivery_status_id'] == 10) {
                    $voucher->store_status_id = 2;
                    // if ($voucher->delivery_status_id == 8) {
                    if ($voucher->delivery_counter == 0) {
                        $voucher->delivery_status_id = 2;
                    } elseif ($voucher->delivery_counter == 1) {
                        $voucher->delivery_status_id = 3;
                    } elseif ($voucher->delivery_counter >= 2) {
                        $voucher->delivery_status_id = 4;
                    }
                    // }
                    // if ($voucher->delivery_counter == 1) {
                    //     $voucher->delivery_status_id = 2;
                    // } elseif ($voucher->delivery_counter == 2) {
                    //     $voucher->delivery_status_id = 3;
                    // } else {
                    //     $voucher->delivery_status_id = 3;
                    // }
                    // elseif ($voucher->delivery_counter == 3) {
                    //     // if ($voucher->delivery_status_id == 3) {
                    //     $voucher->delivery_status_id = 9;
                    //     DeliSheetVoucher::where('delisheet_id', $deliSheet->id)
                    //         ->where('voucher_id', $voucher->id)
                    //         ->update(['delivery_status' => 0, 'return' => 1]);
                    //     // }
                    // }
                    //for voucher histories
                    // if ($voucher->delivery_counter != 3) {
                    //     DeliSheetVoucher::where('delisheet_id', $deliSheet->id)
                    //         ->where('voucher_id', $voucher->id)
                    //         ->update(['delivery_status' => 1]);
                    // }
                }

                // change status deli sheet voucher
                if ($voucherId['delivery_status_id'] == 9) {
                    DeliSheetVoucher::where('delisheet_id', $deliSheet->id)
                        ->where('voucher_id', $voucher->id)
                        ->update(['delivery_status' => 0, 'return' => 1, 'note' => $note]);
                } elseif ($voucherId['delivery_status_id'] == 8) {
                    DeliSheetVoucher::where('delisheet_id', $deliSheet->id)
                        ->where('voucher_id', $voucher->id)
                        ->update(['delivery_status' => 1, 'note' => null]);
                    $voucher->end_date =  date('Y-m-d H:i:s');
                    if ($deliSheet->is_commissionable && isHero($deliSheet)) {
                        $voucher->delivery_commission = isFreelancer($deliSheet->delivery)
                            ? $voucher->receiver_zone->outsource_rate
                            : (isFreelancerCar($deliSheet->delivery)
                                ? $voucher->receiver_zone->outsource_car_rate
                                : $voucher->receiver_zone->zone_commission);
                    }
                } elseif ($voucherId['delivery_status_id'] == 10) {
                    DeliSheetVoucher::where('delisheet_id', $deliSheet->id)
                        ->where('voucher_id', $voucher->id)
                        ->update(['cant_deliver' => 1, 'delivery_status' => 0, 'note' => $note]);
                }


                if ($voucherId['delivery_status_id'] != 10) {
                    $voucher->delivery_status_id = $voucherId['delivery_status_id'];
                    //$voucher->deli_payment_status = 1;
                }

                if ($voucherId['delivery_status_id'] != 8) {
                    $voucher->outgoing_status = null;
                } else {
                    $voucher->store_status_id = 7;
                    $voucher->call_status_id =  6;
                    $voucher->delivered_date = ($voucher->delivered_date) ? $voucher->delivered_date : date('Y-m-d H:i:s');
                }

                if ($voucherId['delivery_status_id'] == 9) {
                    // $voucher->is_return = 1;
                    $merchant_account = $voucher->pickup->sender->account;
                    $journals = $voucher->journals->where('status', 0);
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
                    $return_percentage = ($voucher->origin_city_id != $voucher->sender_city_id) ? 100 : getReturnPercentage();
                    $return_amount = $delivery_amount * ($return_percentage / 100);

                    $branch_account =  $voucher->sender_city->branch->account;
                    $hq_account =  $hq_account = Account::where('accountable_type', 'HQ')->firstOrFail();

                    if ($payment_id == 9 || $payment_id == 10) {
                        $journalRepository->JournalCreateData($branch_account->id, $hq_account->id, $delivery_amount, $voucher, 'Voucher', 1);

                        if ($voucher->origin_city_id == $voucher->sender_city_id) {
                            $journalRepository->JournalCreateData($merchant_account->id, $branch_account->id, $return_amount, $voucher, 'Voucher', 1);
                            $journalRepository->JournalCreateData($hq_account->id, $merchant_account->id, $return_amount, $voucher, 'Voucher');
                        } else {
                            // $journalRepository->JournalCreateData($merchant_account->id, $hq_account->id, $return_amount, $voucher, 'Voucher', 1);
                        }

                        // update branch and merchant balance because of prepaid type
                        $branch_account->balance -= $delivery_amount;
                        $branch_account->save();
                        // if ($hq_account->isDirty()) {
                        //     $hq_account->balance -= $delivery_amount;
                        //     $hq_account->save();
                        // }

                        // if ($merchant_account->isDirty()) {
                        // $merchant_account->balance -= $delivery_amount;
                        // $merchant_account->save();
                        // }
                    } else {
                        $journalRepository->JournalCreateData($merchant_account->id, $hq_account->id, $return_amount, $voucher, 'Voucher');
                    }

                    $voucher->return_fee = $return_amount;
                    $voucher->return_type = "Noramal Return Fee";
                }

                if ($voucher->isDirty()) {
                    $voucher->updated_by = auth()->user()->id;
                    $voucher->save();
                }

                if ($voucherId['delivery_status_id'] === 8) {
                    $collect_amount += $voucher->receiver_amount_to_collect;
                    $delivery_commission += $voucher->delivery_commission;
                    $customerRepository = new CustomerRepository();
                    $customer = $customerRepository->rate($voucher->receiver, 'success');
                    if ($voucher->platform === 'Merchant App' || $voucher->platform === 'Merchant Dashboard') {
                        $merchantRepository = new MerchantRepository();
                        $merchantRepository->calculate_reward($voucher);
                    }
                }
            }
        }
        $total_amount = $collect_amount - ($lunch_amount + $delivery_commission);
        $deliSheet->total_amount = $total_amount;
        $deliSheet->collect_amount = $collect_amount;

        // //Calculate Staff commission and lunch
        // $staff = Staff::findOrFail($staff_id);
        // $staff_account = $staff->account;

        // if (!$staff->account) {
        //     $staff_account = $journalRepository->create_account($staff, 'Staff');
        // }
        // // if ($lunch_amount > 0) {
        // //     $journalRepository->JournalCreateData($authBranchAccountId, $staff_account->id, $lunch_amount, $deliSheet, 'DeliSheet', 1);
        // // }
        // if ($commission_amount > 0) {
        //     $journalRepository->JournalCreateData($authBranchAccountId, $staff_account->id, $commission_amount, $deliSheet, 'DeliSheet', 1);
        // }
        if (isset($data['vouchers'])) {
            $deliveredVouchers = array_filter($data['vouchers'], function ($value) {
                return $value['delivery_status_id'] == 8;
            });
            $alreadyEarned = $this->membershipContract->checkCommission($deliSheet->delivery, $deliSheet);
            if ($deliSheet->getQtyAttribute() > 0 && $deliSheet->is_commissionable && isHero($deliSheet) && $alreadyEarned < 1) {
                $deliSheet->commission_amount =  $delivery_commission;
                $this->membershipContract->earnCommission($deliSheet, $deliveredVouchers);
                $this->membershipContract->loggingCommission($deliSheet->delivery, $deliSheet, $deliSheet->zone, count($deliveredVouchers));
            }
            $commissionVouchers = DelisheetVoucher::where('delisheet_id', $deliSheet->id)->where('delivery_status', 1)->where('is_came_from_mobile', 1)->count();
            if (
                $deliSheet->getQtyAttribute() > 0 && $deliSheet->is_pointable && isHero($deliSheet)
                && !isBlackList($deliSheet->delivery) && !isFreelancerCar($deliSheet->delivery)
            ) {
                $this->membershipContract->earnPointPerVoucher($deliSheet, $commissionVouchers, 'Delisheet');
            }
        }
        // voucher is finished for this DelisheetVoucher
        DeliSheetVoucher::where('delisheet_id', $deliSheet->id)
            ->where('voucher_id', $voucher->id)
            ->update(['finished_date' => date('Y-m-d H:i:s')]);
        if ($deliSheet->isDirty()) {
            $deliSheet->updated_by = auth()->user()->id;
            $deliSheet->save();
        }
        return $deliSheet->refresh();
    }

    /**
     * @param DeliSheet $deliSheet
     */
    public function destroy(DeliSheet $deliSheet)
    {
        // $deleted = $this->deleteById($deliSheet->id);
        $deleted = $deliSheet->delete();

        if ($deleted) {
            $deliSheet->deleted_by = auth()->user()->id;
            $deliSheet->save();
        }
    }

    /**
     * @param DeliSheet  $deliSheet
     * @param array $data
     *
     * @return mixed
     */
    public function remove_vouchers(DeliSheet $deliSheet, array $data): DeliSheet
    {
        if (isset($data['vouchers'])) {
            foreach ($data['vouchers'] as $voucherId) {
                $voucher = Voucher::findOrFail($voucherId['id']);

                $qty = $deliSheet->qty;
                $deliSheetVoucher = DeliSheetVoucher::where('delisheet_id', $deliSheet->id)
                    ->where('voucher_id', $voucher->id)
                    ->firstOrFail();
                $deleted = $deliSheet->vouchers()->detach($voucherId['id']);

                if ($deleted) {
                    $voucher->outgoing_status = null;
                    $voucher->store_status_id = 4;
                    if ($voucher->delivery_counter == 0) {
                        $voucher->delivery_status_id = 1;
                    } elseif ($voucher->delivery_counter == 1) {
                        $voucher->delivery_status_id = 2;
                    } elseif ($voucher->delivery_counter == 2) {
                        $voucher->delivery_status_id = 3;
                    } elseif ($voucher->delivery_counter > 2) {
                        $voucher->delivery_status_id = 4;
                    }
                    $voucher->delivery_counter -= 1;
                    $qty -= 1;
                }

                $deliSheet->qty = $qty;

                if ($voucher->isDirty()) {
                    $voucher->updated_by = auth()->user()->id;
                    $voucher->save();
                    $voucher->voucherSheetFire($deliSheet->delisheet_invoice, 'remove_delisheet_voucher');
                }

                if ($deliSheet->isDirty()) {
                    $deliSheet->updated_by = auth()->user()->id;
                    $deliSheet->save();
                    $deliSheet->delisheetVoucherFire($voucher->voucher_invoice, 'remove_delisheet_voucher');
                }
            }
        }
        return $deliSheet->refresh();
    }

    public function add_vouchers(DeliSheet $deliSheet, array $data): DeliSheet
    {
        if (isset($data['vouchers'])) {
            foreach ($data['vouchers'] as $voucher) {
                $note = null;
                if (isset($voucher['deli_sheet_voucher_note'])) {
                    $note = getConvertedString($voucher['deli_sheet_voucher_note']);
                }
                $qty = $deliSheet->qty;
                $deliSheet->vouchers()->attach($voucher['id'], [
                    'note' => $note,
                    'priority' => $voucher['deli_sheet_voucher_priority']
                ]);
                $voucher = Voucher::findOrFail($voucher['id']);
                $voucher->outgoing_status = 0;
                if ($voucher->delivery_counter == 0) {
                    $voucher->delivery_status_id = 2;
                } elseif ($voucher->delivery_counter == 1) {
                    $voucher->delivery_status_id = 3;
                } else {
                    $voucher->delivery_status_id = 4;
                }
                $voucher->delivery_counter += 1;
                $voucher->store_status_id = 5;
                $voucher->save();
                $voucher->voucherSheetFire($deliSheet->delisheet_invoice, 'new_delisheet_voucher');
                $deliSheet->qty = $qty + 1;
                $deliSheet->save();
                $deliSheet->delisheetVoucherFire($voucher->voucher_invoice, 'new_delisheet_voucher');
            }
        }

        return $deliSheet->refresh();
    }

    public function add_scan_vouchers(DeliSheet $deliSheet, $voucher): DeliSheet
    {
        $qty = $deliSheet->qty;
        $deliSheet->vouchers()->attach($voucher->id);

        $voucher->outgoing_status = 0;
        if ($voucher->delivery_counter == 0) {
            $voucher->delivery_status_id = 2;
        } elseif ($voucher->delivery_counter == 1) {
            $voucher->delivery_status_id = 3;
        } else {
            $voucher->delivery_status_id = 4;
        }
        $voucher->delivery_counter += 1;
        $voucher->store_status_id = 5;
        $voucher->save();
        $voucher->voucherSheetFire($deliSheet->delisheet_invoice, 'new_delisheet_voucher');
        $deliSheet->qty = $qty + 1;
        $deliSheet->save();
        $deliSheet->delisheetVoucherFire($voucher->voucher_invoice, 'new_delisheet_voucher');

        return $deliSheet->refresh();
    }

    /**
     * @param DeliSheet  $deliSheet
     * @param array $data
     *
     * @return mixed
     */
    public function change_delivery(DeliSheet $deliSheet, array $data): DeliSheet
    {
        $delivery = Staff::find($data['delivery_id']);
        $deliSheet->delivery_id = $delivery->id;
        if ($delivery->zone_id) {
            $deliSheet->zone_id = $delivery->zone_id;
        }
        $deliSheet->note = getConvertedString($data['note']);
        $deliSheet->date = isset($data['date']) ? getConvertedString($data['date']) : $deliSheet->date;
        $deliSheet->courier_type_id = isset($data['courier_type_id']) ? $data['courier_type_id'] : $deliSheet->courier_type_id;
        $deliSheet->is_commissionable = isset($data['is_commissionable']) ? $data['is_commissionable'] : $deliSheet->is_commissionable;
        $deliSheet->is_pointable = isset($data['is_pointable']) ? $data['is_pointable'] : $deliSheet->is_pointable;

        if ($deliSheet->isDirty()) {
            $deliSheet->save();
        }

        return $deliSheet->refresh();
    }
}
