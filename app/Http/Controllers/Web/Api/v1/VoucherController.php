<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\City;
use App\Models\Zone;
use App\Models\Pickup;
use App\Models\Voucher;
use App\Models\Merchant;
use App\Models\DeliSheet;
use App\Models\Attachment;
use App\Models\PaymentType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Exports\VoucherExport;
use App\Models\DeliSheetVoucher;
use App\Exports\DraftVoucherExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Exports\AgentWaybillVoucherSheet;
use App\Http\Requests\Transaction\FileRequest;
use App\Http\Resources\Voucher\VoucherResource;
use App\Http\Resources\Voucher\VoucherCollection;
use App\Http\Requests\Voucher\CloseVoucherRequest;
use App\Repositories\Web\Api\v1\AccountRepository;
use App\Repositories\Web\Api\v1\VoucherRepository;
use App\Http\Requests\Voucher\CreateVoucherRequest;
use App\Http\Requests\Voucher\ImportVoucherRequest;
use App\Http\Requests\Voucher\UpdateVoucherRequest;
use App\Http\Requests\Voucher\UpdateWaybillRequest;
use App\Http\Resources\Attachment\AttachmentResource;

//use App\Http\Requests\Voucher\UpdateVoucherRequest;
use App\Http\Requests\Voucher\ManualCloseVoucherRequest;
use App\Http\Requests\Voucher\UpdateStatusVoucherRequest;
use App\Http\Resources\DraftVoucher\DraftVoucherResource;
use App\Http\Resources\DraftVoucher\DraftVoucherCollection;
use App\Http\Resources\DashboardTracking\TrackingVoucherCollection;

class VoucherController extends Controller
{
    protected $voucherRepository;

    public function __construct(VoucherRepository $voucherRepository)
    {
        $this->voucherRepository = $voucherRepository;
    }

    public function index()
    {
        // 'pickup.sender.staff'

        $agent_city_id = request()->get('agent_city_id');

        // if ($agent_city_id) {
        //     $city_id = $agent_city_id;
        // }else{
        //     $city_id = auth()->user()->city_id;
        // }

        if (request()->has('export') && request()->has('type') && request()->get('type') == 'agent_voucher') {
            $filename = 'agent_waybill_voucher.xlsx';
            Excel::store(new AgentWaybillVoucherSheet, $filename, 'public', null, [
                'visibility' => 'public',
            ]);
            $file = storage_path('/app/public/agent_waybill_voucher.xlsx');
            return response()->download($file)->deleteFileAfterSend();
        }

        if (request()->has('export')) {
            $filename = 'voucher.xlsx';
            Excel::store(new VoucherExport, $filename, 'public', null, [
                'visibility' => 'public',
            ]);
            $file = storage_path('/app/public/voucher.xlsx');
            return response()->download($file)->deleteFileAfterSend();
        }

        $vouchers = Voucher::with([
            'pickup', 'pickup.sender.staff', 'pickup.sender', 'customer', 'payment_type', 'sender_city', 
            'receiver_city', 'sender_zone', 'receiver_zone','from_agent','to_agent',
            'call_status', 'delivery_status', 'store_status', 'payment_status', 
            'pending_returning_actor' => function ($query) {
                $query->withTrashed();
            }, 'attachments'
        ])
            ->where(function ($query) use ($agent_city_id) {
                if (auth()->user()->department->department === 'Agent') {
                    if ($agent_city_id) {
                        $query->where('sender_city_id', $agent_city_id);
                            // ->orWhere('sender_city_id', $agent_city_id);
                    } else {
                        $query;
                    }
                } elseif (auth()->user()->hasRole('HQ')) {
                    $query;
                } else {
                    $query->where('origin_city_id', auth()->user()->city_id)
                        ->orWhere('sender_city_id', auth()->user()->city_id);
                }
            })
            ->filter(request()->only([
                'voucher_invoice', 'date', 'receiver_city', 'receiver_zone', 'sender', 'receiver',
                'call_status', 'delivery_status', 'receiver_name', 'receiver_phone', 'delivered_date',
                'start_date', 'end_date', 'from_city_id', 'to_city_id', 'thirdparty_invoice',
                'outgoing_status', 'store_status', 'try_to_deliver', 'waybill_id', 'waybill_invoice',
                'sender_phone', 'sender_name', 'receiver_amount_to_collect', 'waybill_start_date',
                'waybill_end_date', 'voucher_type', 'postpone_date', 'postpone', 'pending_return',
                'from_agent_id', 'to_agent_id'
            ]))
            ->whereNotNull('pickup_id')
            ->order(request()->only([
                'sortBy', 'orderBy'
            ]));

        if (request()->get('associated_merchant') === "true") {
            $merchants_id = Merchant::where('staff_id', auth()->user()->id)->get()->pluck('id');
            $vouchers = $vouchers->whereHas('pickup', function ($query) use ($merchants_id) {
                $query->where('sender_type', 'Merchant')
                    ->whereIn('sender_id', $merchants_id);
            });
        }

        if (request()->has('paginate')) {
            $vouchers = $vouchers->paginate(request()->get('paginate'));
        } else {
            $vouchers = $vouchers->get();
        }

        return new VoucherCollection($vouchers);
    }

    public function store(CreateVoucherRequest $request)
    {
        if(str_contains($request->url(), 'old.marathonmyanmar')){
            return response()->json(
                ['status' => 2, 'message' => 'Cannot create in old url.'],
                Response::HTTP_OK
            );
        }
        $pickup = Pickup::findOrFail($request->input('pickup_id'));
        if ($pickup->sender_type === 'Merchant' && $pickup->sender->is_corporate_merchant && $request->payment_type_id != 2) {
            return response()->json(['status' => 2, 'message' => 'Payment Type invalid for this coporate merchant']);
        }
        if (!$pickup->is_closed) {
            $request['platform'] = 'Marathon Dashboard';
            $response = $this->voucherRepository->create($request->all());
            // dd(auth()->user()->city_id);
            if ($response['status'] === 1) {
                // will be observed and stored by observer
                // $this->trackingVoucherRepository->create([
                //     'voucher_id'      => $response['data']->id,
                //     'city_id'      => auth()->user()->city_id,
                //     'tracking_status_id'     => 1,
                //     'created_by' => auth()->user()->id

                // ]);

                return new VoucherResource($response['data']->load([
                    'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                    'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'pickup.sender.staff',
                    'call_status', 'delivery_status', 'delegate_duration', 'delegate_person', 'store_status', 'parcels', 'pickup', 'pickup.sender', 'pickup.opened_by_staff' => function ($query) {
                        $query->withTrashed();
                    }
                ]));
            } else {
                return response()->json(['status' => $response['status'], 'message' => $response['message']]);
            }
        }
        return response()->json(['status' => 2, 'message' => 'Cannot create new voucher coz this pickup is already closed ']);
    }

    public function show(Voucher $voucher)
    {
        // 'pickup.sender.staff',
        return new VoucherResource($voucher->load([
            'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'pickup.sender.staff',
            'call_status', 'delivery_status', 'delegate_duration', 'delegate_person', 'store_status', 'parcels',
            'pickup', 'pickup.sender', 'pickup.created_by', 'pickup.assigned_by',
            'pickup.pickuped_by', 'pending_returning_actor' => function ($query) {
                $query->withTrashed();
            }, 'attachments'
        ]));
    }

    public function update(UpdateVoucherRequest $request, Voucher $voucher)
    {
        $pickup = $voucher->pickup;
        if ($pickup->sender_type === 'Merchant' && $pickup->sender->is_corporate_merchant && $request->payment_type_id != 2) {
            return response()->json(['status' => 2, 'message' => 'Payment Type invalid for this coporate merchant']);
        }
        if ($voucher->is_closed) {
            return response()->json(['status' => 2, "message" => "Cannot update voucher is already closed or return"]);
        } else {
            $response = $this->voucherRepository->update($voucher, $request->all());
            if ($response['status'] === 1) {
                return new VoucherResource($response['data']->load([
                    'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                    'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'pickup.sender.staff',
                    'call_status', 'delivery_status', 'delegate_duration', 'delegate_person', 'store_status', 'parcels',
                    'pickup', 'pickup.sender', 'pickup.opened_by_staff', 'pending_returning_actor' => function ($query) {
                        $query->withTrashed();
                    }
                ]));
            } else {
                return response()->json(['status' => $response['status'], 'message' => $response['message']]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Voucher $voucher)
    {
        $pickup = $voucher->pickup;
        $this->voucherRepository->destroy($voucher);

        return response()->json(['status' => 1, 'total_prepaid_amount' => $pickup->vouchers()->prepaidAmount()], Response::HTTP_OK);
    }

    public function closed(Voucher $voucher)
    {
        if ($voucher->is_closed || $voucher->is_return) {
            return response()->json(['status' => 2, "message" => "Voucher is already closed or returned"]);
        } else {
            $voucher = $this->voucherRepository->closed($voucher);
            return new VoucherResource($voucher->load([
                'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
                'call_status', 'delivery_status', 'delegate_duration', 'delegate_person', 'store_status', 'parcels', 'pickup', 'pickup.sender', 'pickup.opened_by_staff' => function ($query) {
                    $query->withTrashed();
                }
            ]));
        }
    }

    public function manual_closed(ManualCloseVoucherRequest $request)
    {
        foreach ($request->get('vouchers') as $v) {
            $voucher = Voucher::find($v);
            if ($voucher) {
                if (!$voucher->is_closed) {
                    $voucher = $this->voucherRepository->closed($voucher);
                }

                $voucher->deli_payment_status = 1;
                $voucher->delivered_date = date('Y-m-d H:i:s');

                $accountRepository = new AccountRepository();
                $accountRepository->confirm_voucher($voucher);

                $voucher->save();
            }
        }

        return response()->json([
            'status' => 1, 'message' => 'OK .'
        ], Response::HTTP_OK);
    }

    public function return(Voucher $voucher)
    {
        $user = auth()->user();

        if ($voucher->delivery_status_id == 9) {
            return response()->json(['status' => 2, "message" => "Voucher is already return"]);
        }
        if ($voucher->store_status_id == 1) {
            return response()->json(['status' => 2, "message" => "Voucher havn't received"]);
        }

        if ($voucher->is_closed || $voucher->is_return) {
            return response()->json(['status' => 2, "message" => "Voucher is already closed or returned"]);
        }

        if ($voucher->origin_city_id != $user->city_id) {
            return response()->json(['status' => 2, "message" => "Cannot return"]);
        }

        $delisheet_voucher = $voucher->delisheets()->latest()->first();
        if ($delisheet_voucher) {
            if (!$delisheet_voucher->is_closed) {
                return response()->json(['status' => 2, "message" => "Cannot return coz voucher is already assigned in delisheet"]);
            } else {
                if (!$delisheet_voucher->deli_sheet_vouchers->cant_deliver) {
                    return response()->json(['status' => 2, "message" => "Cannot return coz voucher is not can't deliver in delisheet."]);
                }
            }
        }

        $waybill_voucher = $voucher->waybills()->latest()->first();
        if ($waybill_voucher) {
            if (!$waybill_voucher->is_closed || !$waybill_voucher->is_received) {
                return response()->json(['status' => 2, "message" => "Cannot return coz voucher is already assigned in Waybill."]);
            }
        }
        $voucher = $this->voucherRepository->return($voucher);
        return new VoucherResource($voucher->load([
            'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
            'call_status', 'delivery_status', 'delegate_duration', 'delegate_person', 'store_status',
            'parcels', 'pickup', 'pickup.sender', 'pickup.opened_by_staff', 'pending_returning_actor' => function ($query) {
                $query->withTrashed();
            }
        ]));
    }

    public function undo_return(Voucher $voucher)
    {
        if ($voucher->delivery_status_id == 9 && $voucher->is_manual_return) {
            if($voucher->returnsheet_vouchers->count() > 0){
                return response()->json(['status' => 2, "message" => "Cannot undo return coz voucher is already exit in retrun sheet."]);
            }
            $delisheet_voucher = $voucher->delisheets()->latest()->first();
            if ($delisheet_voucher) {
                if (!$delisheet_voucher->is_closed) {
                    return response()->json(['status' => 2, "message" => "Cannot return coz voucher is already assigned in delisheet"]);
                } else {
                    if (!$delisheet_voucher->deli_sheet_vouchers->cant_deliver) {
                        return response()->json(['status' => 2, "message" => "Cannot return coz voucher is not can't deliver in delisheet."]);
                    }
                }
            }

            $waybill_voucher = $voucher->waybills()->latest()->first();
            if ($waybill_voucher) {
                if (!$waybill_voucher->is_closed || !$waybill_voucher->is_received) {
                    return response()->json(['status' => 2, "message" => "Cannot return coz voucher is already assigned in Waybill."]);
                }
            }
            $voucher->is_closed = 0;
            $voucher->delivery_status_id = 2;
            $voucher->is_manual_return = 0;
            $voucher->return_fee = 0;
            $voucher->pending_returning_date = null;
            $voucher->pending_returning_actor_id = null;
            $voucher->pending_returning_actor_type = null;
            $voucher->save();
            //$voucher->journals()->delete();
            return response()->json(['status' => 1, "message" => "Success"]);
        } else {
            return response()->json(['status' => 2, "message" => "Cannot undo return bcoz this voucher is not return"]);
        }
    }

    public function show_merchant_attachment(Attachment $attachment)
    {
        $attachment->is_show_merchant = 1;
        $attachment->save();

        return new AttachmentResource($attachment);
    }

    public function unshow_merchant_attachment(Attachment $attachment)
    {
        $attachment->is_show_merchant = 0;
        $attachment->save();

        return new AttachmentResource($attachment);
    }

    public function update_status(Request $request, Voucher $voucher)
    {
        $voucher = $this->voucherRepository->update_status($voucher, $request->all());
        return new VoucherResource($voucher->load([
            'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
            'call_status', 'delivery_status', 'delegate_duration', 'delegate_person',
            'store_status', 'parcels', 'pickup', 'pickup.sender', 'pickup.opened_by_staff',
            'pending_returning_actor'
            => function ($query) {
                $query->withTrashed();
            }
        ]));
    }
    public function updateWaybillVoucher(UpdateWaybillRequest $request, Voucher $voucher)
    {
        if($voucher->is_closed){
            return response()->json(['status' => 2, "message" => "Voucher is already closed."]);
        }
        $voucher = $this->voucherRepository->update_status($voucher, $request->all());
        return new VoucherResource($voucher->load([
            'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
            'call_status', 'delivery_status', 'delegate_duration', 'delegate_person', 'store_status',
            'parcels', 'pickup', 'pickup.sender', 'pickup.opened_by_staff', 'pending_returning_actor'
            => function ($query) {
                $query->withTrashed();
            }
        ]));
    }

    public function importVouchers(ImportVoucherRequest $request)
    {
        foreach ($request['vouchers'] as $data) {
            $receiver_zone = Zone::where('name', $data['receiver_zone'])->first();
            if (!$receiver_zone) {
                $receiver_zone = Zone::where('city_id', getBranchCityId())->first();
            }
            $receiver_city = City::where('name', $data['receiver_city'])->first();
            if (!$receiver_city) {
                $receiver_city = City::find($receiver_zone->city_id);
            }
            $payment_type = isset($data['payment_type']) ? $data['payment_type'] : 'Delivery Fee Included';
            $payment_type_id = PaymentType::where('name', $payment_type)->first()->id;
            $data['receiver_city_id'] = $receiver_city->id;
            $data['receiver_zone_id'] = $receiver_zone->id;
            $data['payment_type_id'] = $payment_type_id;
            $data['store_status_id'] = 1;
            $data['platform'] = 'Marathon Dashboard';

            $response = $this->voucherRepository->create($data);
            // will be observed and stored by observer
            // if ($response['status'] === 1) {
            //     $this->trackingVoucherRepository->create([
            //         'voucher_id'      => $response['data']->id,
            //         'city_id'      => auth()->user()->city_id,
            //         'tracking_status_id'     => 1,
            //         'created_by' => auth()->user()->id
            //     ]);
            // } else {
            //     return response()->json(['status' => $response['status'], 'message' => $response['message']]);
            // }
        }
        return response()->json(['status' => $response['status'], 'message' => $response['message']]);
    }

    /**
     * Upload Voucher's Image
     */
    public function upload(Voucher $voucher, FileRequest $request)
    {
        if ($request->hasFile('file') && $file = $request->file('file')) {
            $voucher = $this->voucherRepository->upload($voucher, $file);
        }

        return new VoucherResource($voucher->load([
            'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'pickup.sender.staff',
            'call_status', 'delivery_status', 'delegate_duration', 'delegate_person', 'store_status', 'parcels',
            'pickup', 'pickup.sender', 'pickup.created_by', 'pickup.assigned_by', 'pickup.pickuped_by' => function ($query) {
                $query->withTrashed();
            }, 'attachments'
        ]));
    }

    public function tracking_voucher(Voucher $voucher)
    {
        return new TrackingVoucherCollection($voucher->tracking_vouchers()->orderBy('id', 'desc')->get());
    }

    public function draft_vouchers()
    {
        $agent_city_id = request()->get('agent_city_id');

        if (request()->has('export')) {
            $filename = 'voucher.xlsx';
            Excel::store(new DraftVoucherExport, $filename, 'public', null, [
                'visibility' => 'public',
            ]);
            $file = storage_path('/app/public/voucher.xlsx');
            return response()->download($file);
        }

        $vouchers = Voucher::with([
            'pickup', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'created_by_merchant',
            'call_status', 'delivery_status', 'store_status', 'payment_status', 'parcels' => function ($query) {
                $query->withTrashed();
            }
        ])
            ->whereNotNull('receiver_id')
            ->where(function ($query) use ($agent_city_id) {
                if (auth()->user()->hasRole('Agent')) {
                    if ($agent_city_id) {
                        $query->where('origin_city_id', $agent_city_id)
                            ->orWhere('sender_city_id', $agent_city_id);
                    } else {
                        $query;
                    }
                } elseif (auth()->user()->hasRole('HQ')) {
                    $query;
                } else {
                    $query->where('origin_city_id', auth()->user()->city_id)
                        ->orWhere('sender_city_id', auth()->user()->city_id);
                }
            })
            ->filterDraft(request()->only([
                'voucher_invoice', 'date', 'receiver_city', 'receiver_zone', 'sender', 'receiver',
                'call_status', 'delivery_status', 'receiver_name', 'receiver_phone', 'delivered_date',
                'start_date', 'end_date', 'from_city_id', 'to_city_id', 'thirdparty_invoice',
                'outgoing_status', 'store_status', 'try_to_deliver', 'waybill_id', 'waybill_invoice',
                'sender_phone', 'sender_name', 'receiver_amount_to_collect', 'waybill_start_date',
                'waybill_end_date', 'voucher_type'
            ]))
            ->order(request()->only([
                'sortBy', 'orderBy'
            ]));

        if (request()->has('paginate')) {
            $vouchers = $vouchers->paginate(request()->get('paginate'));
        } else {
            $vouchers = $vouchers->get();
        }

        return new DraftVoucherCollection($vouchers);
    }

    public function draft_voucher_detail(Voucher $voucher)
    {
        return new DraftVoucherResource($voucher->load([
            'pickup', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
            'call_status', 'delivery_status', 'store_status', 'payment_status', 'parcels'
        ]));
    }

    public function scanVoucherReceive()
    {
        $agent_city_id = request()->get('agent_city_id');
        $voucher_no = request()->get('invoice_no');
        $vouchers = Voucher::with([
            'pickup', 'pickup.sender.staff', 'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'call_status', 'delivery_status', 'store_status', 'payment_status', 'pending_returning_actor' => function ($query) {
                $query->withTrashed();
            }
        ])->where(function ($query) use ($agent_city_id) {
            if (auth()->user()->department->department === 'Agent') {
                if ($agent_city_id) {
                    $query->where('origin_city_id', $agent_city_id)
                        ->orWhere('sender_city_id', $agent_city_id);
                } else {
                    $query;
                }
            } elseif (auth()->user()->hasRole('HQ')) {
                $query;
            } else {
                $query->where('origin_city_id', auth()->user()->city_id)
                    ->orWhere('sender_city_id', auth()->user()->city_id);
            }
        })->whereNotNull('pickup_id');
        
        // if (Str::length($voucher_no) > 8) {
        $no = substr($voucher_no, 0, 2);
        if ($no != 'VN') {
            $voucher = $vouchers->where('thirdparty_invoice', $voucher_no)->first();
        } else {
            $voucher = $vouchers->where('voucher_invoice', $voucher_no)->first();
        }

        if ($voucher) {
            if ($voucher->store_status_id != 1) {
                return response()->json([
                    'status' => 2, 'message' => 'Voucher is already received.'
                ], Response::HTTP_OK);
            } else {
                $voucher->store_status_id = 2;
                $voucher->save();
            }
        } else {
            return response()->json([
                'status' => 2, 'message' => 'Voucher does not exit'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => 1,
            'data'   => new VoucherResource($voucher),
            'message' => 'Voucher has been successfully received.'
        ], Response::HTTP_OK);
    }
}
