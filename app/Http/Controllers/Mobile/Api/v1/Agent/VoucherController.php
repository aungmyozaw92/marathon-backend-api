<?php

namespace App\Http\Controllers\Mobile\Api\v1\Agent;

use Carbon\Carbon;
use App\Models\Account;
use App\Models\Journal;
use App\Models\Message;
use App\Models\Voucher;
use App\Models\Waybill;
use App\Jobs\AgentRewardJob;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\WaybillVoucher;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\AccountRepository;
use App\Repositories\Web\Api\v1\JournalRepository;
use App\Repositories\Web\Api\v1\VoucherRepository;
use App\Repositories\Web\Api\v1\WaybillRepository;
use App\Repositories\Web\Api\v1\CustomerRepository;
use App\Repositories\Web\Api\v1\MerchantRepository;
use App\Http\Requests\Mobile\Agent\Voucher\UpdateRequest;
use App\Http\Resources\Mobile\Agent\Voucher\VoucherResource;
use App\Http\Resources\Mobile\Agent\Voucher\VoucherCollection;
use App\Http\Resources\Mobile\Agent\DeliveredVoucher\DeliveredVoucherCollection;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::agentWaybillVoucher(request()->only(['cant_deliver']))
            ->get();
        return new VoucherCollection($vouchers->load([
            'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone',
            'receiver_zone', 'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
            'delivery_status', 'pickup', 'pickup.sender.staff'
            // 'pickup.sender', 'pickup.sender.staff','pickup.opened_by_staff', 'attachments'
            => function ($query) {
                $query->withTrashed();
            }
        ]));
    }
    public function finish_vouchers()
    {
        $vouchers = Voucher::AgentWaybillVoucherListWithStatusId(['delivery_status_id' => 8])->get();
        return new DeliveredVoucherCollection($vouchers->load([
            'customer', 'receiver_zone'
            => function ($query) {
                $query->withTrashed();
            }
        ]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Voucher  $waybill
     * @return \Illuminate\Http\Response
     */
    public function show(Voucher $voucher)
    {
        return new VoucherResource($voucher->load([
            'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'call_status', 'delivery_status',
            'store_status', 'parcels', 'pickup', 'pickup.sender', 'pickup.sender.staff', 'attachments' => function ($query) {
                $query->withTrashed();
            }
        ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Waybill  $waybill
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Voucher $voucher)
    {
        $agent = auth()->user();
        if (!$voucher->is_closed) {
            $voucher->delivery_status_id = $request->get('delivery_status_id');
            $voucher->delivered_date = date('Y-m-d H:i:s');
            $voucher->save();

            $voucher_exists = Journal::where('resourceable_id', $voucher->id)->where('resourceable_type', 'Voucher')->exists();
            if ($voucher_exists) {
                return response()->json([
                    'status' => 2, 'message' => 'Voucher is already closed in this Waybill.'
                ], Response::HTTP_OK);
            }
            // save reason and note
            $waybill_voucher = WaybillVoucher::where('waybill_id', $request->waybill_id)
                ->where('voucher_id', $voucher->id)->first();
            //if ($voucher->delivery_status_id == 10) {
            // if ($voucher->delivery_status_id == 10) {
            //     $waybill_voucher_note = null;
            //     if (isset($request->notes)) {
            //         $separated_note = str_replace(',', '|', $request->notes);
            //         $waybill_voucher_note = getConvertedString($separated_note);
            //     }
            //     WaybillVoucher::where('waybill_id', $request->waybill_id)
            //         ->where('voucher_id', $voucher->id)
            //         ->update(['note' => $waybill_voucher_note]);
            //     $message_text = "Delivered-" . $waybill_voucher_note;
            //     $messages = $voucher->messages()->create([
            //         'staff_id' => auth()->user()->id,
            //         'message_text' => $message_text
            //     ]);
            //     $agent->messages()->save($messages);
            // }
            if (isset($request->notes) && !empty($request->notes) && !is_null($request->notes)) {
                $waybill_voucher_note = null;
                if ($request->delivery_status_id == 10) {
                    $separated_note = str_replace(',', '|', $request->notes);
                    $waybill_voucher_note = getConvertedString($separated_note);
                    $message_text = "Can't delivered-" . $waybill_voucher_note;
                }
                if ($request->delivery_status_id == 8) {
                    $waybill_voucher_note = getConvertedString($request->notes);
                    $message_text = "Delivered-" . $waybill_voucher_note;
                }
                $waybill_voucher->note = $waybill_voucher_note;
                // WaybillVoucher::where('waybill_id', $request->waybill_id)
                //     ->where('voucher_id', $voucher->id)
                //     ->update(['note' => $waybill_voucher_note,'is_came_from_partner',1]);
                $messages = $voucher->messages()->create([
                    'staff_id' => auth()->user()->id,
                    'message_text' => $message_text
                ]);
                $agent->messages()->save($messages);
            }

            $waybill_voucher->is_came_from_partner = 1;
            $waybill_voucher->save();
            $voucher = $this->agent_confirm($voucher->refresh());

            if ($voucher && $voucher->delivery_status_id == 8) {
                $waybill = Waybill::find($request->waybill_id);
                if ($waybill->received_date) {
                    $receiced_date = \Carbon\Carbon::createFromFormat('d-m-Y', $waybill->received_date->format('d-m-Y'));
                    $delivery_date = \Carbon\Carbon::createFromFormat('d-m-Y', date('d-m-Y'));

                    $different_days = $receiced_date->diffInDays($delivery_date);
                    if ($delivery_date >= $receiced_date && $different_days <= 2) {

                        $agent_badge = $agent->agent_badge;
                        if ($agent_badge->delivery_points > 0) {
                            $agent->rewards += $agent_badge->delivery_points;
                            $agent->save();
                        }
                    }
                }

                if ($agent->agent_badge && $voucher->receiver_amount_to_collect > 0 && $agent->agent_badge->id > 1) {
                    dispatch(new AgentRewardJob($agent, $voucher->receiver_amount_to_collect));
                }
            }
            return new VoucherResource($voucher->load([
                'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'call_status', 'delivery_status',
                'store_status', 'parcels', 'pickup', 'pickup.sender', 'pickup.sender.staff', 'pickup.opened_by_staff', 'attachments' => function ($query) {
                    $query->withTrashed();
                }
            ]));
        }

        return response()->json([
            'status' => 2, 'message' => 'Voucher is already closed.'
        ], Response::HTTP_OK);
    }

    public function agent_confirm($voucher)
    {
        //$journalRepository = new JournalRepository();
        // closed voucher
        if (!$voucher->is_closed && $voucher->delivery_status_id != 10) {
            $voucherRepository = new VoucherRepository();
            $voucher = $voucherRepository->closed($voucher);
        }

        //$payment_id = $voucher->payment_type_id;

        // if ($voucher->delivery_status_id != 10) {
        $voucher->delivery_status_id = $voucher->delivery_status_id;
        //$voucher->deli_payment_status = 1;
        // }
        // record counter
        $voucher->delivery_counter += 1;
        if ($voucher->delivery_status_id != 8) {
            $voucher->outgoing_status = null;
        } else {
            $voucher->deli_payment_status = 1;
            $voucher->store_status_id = 7;
            $voucher->delivered_date = ($voucher->delivered_date) ? $voucher->delivered_date : date('Y-m-d H:i:s');
            $voucher->transaction_date = date('Y-m-d H:i:s');
            $voucher->end_date =  date('Y-m-d H:i:s');
        }

        // if ($voucher->delivery_status_id == 9 || $voucher->delivery_status_id == 9) {
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

        if ($voucher->delivery_status_id == 8) {
            $accountRepository = new AccountRepository();
            $accountRepository->confirm_branch_voucher($voucher);

            $customerRepository = new CustomerRepository();
            $customerRepository->rate($voucher->receiver, 'success');

            if ($voucher->platform === 'Merchant App' || $voucher->platform === 'Merchant Dashboard') {
                $merchantRepository = new MerchantRepository();
                $merchantRepository->calculate_reward($voucher);
            }
        }
        return $voucher;
    }
}
