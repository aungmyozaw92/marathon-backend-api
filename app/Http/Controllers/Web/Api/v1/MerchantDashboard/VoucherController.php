<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\City;
use App\Models\Zone;
use App\Models\Pickup;
use App\Models\Voucher;
use App\Models\Attachment;
use App\Models\PaymentType;
use Illuminate\Http\Response;
use App\Exports\MerchantVoucherData;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MerchantDeliveringVoucherData;
use App\Http\Requests\Transaction\FileRequest;
use App\Http\Resources\Attachment\AttachmentResource;
use App\Exports\MerchantDeliveredReturningVoucherData;
use App\Http\Resources\Mobile\Voucher\VoucherResource;
use App\Http\Resources\Mobile\Voucher\VoucherCollection;
use App\Http\Requests\Mobile\Voucher\CreateVoucherRequest;
use App\Http\Requests\Mobile\Voucher\UpdateVoucherRequest;
use App\Http\Requests\MerchantDashboard\ImportVoucherRequest;
use App\Repositories\Web\Api\v1\MerchantDashboard\VoucherRepository;

//use App\Http\Requests\Voucher\UpdateVoucherRequest;

class VoucherController extends Controller
{
    protected $voucherRepository;

    public function __construct(VoucherRepository $voucherRepository)
    {
        $this->voucherRepository = $voucherRepository;
    }

    public function index()
    {
        if (request()->has('export')) {
            $filename = 'merchant_vouchers.xlsx';
            Excel::store(new MerchantVoucherData, $filename, 'public', null, [
                    'visibility' => 'public',
            ]);
            $file = storage_path('/app/public/merchant_vouchers.xlsx');
            return response()->download($file)->deleteFileAfterSend();
        }

        $vouchers = Voucher::with([
            'pickup', 'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'pickup.sender.staff',
            'call_status', 'delivery_status', 'store_status', 'payment_status', 'delegate_duration', 'delegate_person', 
            'parcels','pending_returning_actor','order' => function ($query) {
                $query->withTrashed();
            }
        ])
            ->where('created_by_id', auth()->user()->id)
            ->where('created_by_type', 'Merchant')
            ->orWhereHas('pickup', function ($qr) {
                $qr->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
            ->filter(request()->only([
                'voucher_invoice', 'date', 'receiver_city', 'receiver_zone', 'sender', 'receiver',
                'call_status', 'delivery_status', 'receiver_name', 'receiver_phone', 'delivered_date',
                'start_date', 'end_date', 'from_city_id', 'to_city_id', 'pickup_date', 'thirdparty_invoice'
            ]))
            ->order(request()->only([
                'sortBy', 'orderBy'
            ]));

        if (request()->has('paginate')) {
            $vouchers = $vouchers->paginate(request()->get('paginate'));
        } else {
            $vouchers = $vouchers->get();
        }

        return new VoucherCollection($vouchers);
    }

    public function store(CreateVoucherRequest $request)
    {
        if(str_contains($request->url(), 'old-')){
            return response()->json(
                ['status' => 2, 'message' => 'Cannot create in old url.'],
                Response::HTTP_OK
            );
        }
        // return response()->json(['status' => 2, "message" => "Voucher service has been temporarily suspended"]);
        // $pickup = Pickup::findOrFail($request->input('pickup_id'));
        // if (!$pickup->is_closed) {
        if(str_contains($request->url(), 'old.marathonmyanmar')){
            return response()->json(
                ['status' => 2, 'message' => 'Cannot create in old url.'],
                Response::HTTP_OK
            );
        }
        $request['platform'] = 'Merchant Dashboard';
        $city = City::find($request['receiver_city_id']);
        if (!auth()->user()->is_root_merchant) {
            if (!$city->is_available_d2d) {
                return response()->json(['status' => 2, 'message' => 'Our service is not available for this destination.']);
            }
        }
        $response = $this->voucherRepository->create($request->all());
        // dd($response);
        if ($response['status'] === 1) {

            return new VoucherResource($response['data']->load([
                'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
                'call_status', 'delivery_status', 'delegate_duration', 'delegate_person', 'store_status', 
                'parcels', 'pickup', 'pickup.sender', 'pickup.opened_by_staff','order' => function ($query) {
                    $query->withTrashed();
                }
            ]));
        } else {
            return response()->json(['status' => $response['status'], 'message' => $response['message']]);
        }
        // }
        // return response()->json(['status' => 2, 'message' => 'Cannot create new voucher coz this pickup is already closed ']);
        // $voucher = $this->voucherRepository->create($request->all());
        // return new VoucherResource($voucher->load([
        //             'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
        //             'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
        //             'call_status', 'delivery_status', 'delegate_duration', 'delegate_person', 'store_status', 'parcels', 'pickup', 'pickup.sender', 'pickup.opened_by_staff' => function ($query) {
        //                 $query->withTrashed();
        //             }
        //         ]));
    }

    public function show(Voucher $voucher)
    {
        return new VoucherResource($voucher->load([
            'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
            'call_status', 'delivery_status', 'delegate_duration', 'delegate_person', 'store_status', 'parcels',
            'pending_returning_actor', 'pickup', 'pickup.sender', 'pickup.opened_by_staff','order' => function ($query) {
                $query->withTrashed();
            }, 'attachments'
        ]));
    }

    public function update(UpdateVoucherRequest $request, Voucher $voucher)
    {
        if ($voucher->is_closed || $voucher->is_picked) {
            return response()->json(['status' => 2, "message" => "Cannot update voucher is already closed or return"]);
        } else {
            $response = $this->voucherRepository->update($voucher, $request->all());
            if ($response['status'] === 1) {
                // $this->trackingVoucherRepository->create([
                //     'voucher_id'      => $response['data']->id,
                //     'tracking_status_id'     => 1,
                // ]);

                return new VoucherResource($response['data']->load([
                    'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                    'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
                    'call_status', 'delivery_status', 'delegate_duration', 'delegate_person', 'store_status', 'parcels', 
                    'pickup', 'pickup.sender', 'pickup.opened_by_staff','pending_returning_actor','order'=> function ($query) {
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
        if (!$voucher->is_picked && !$voucher->is_closed) {
            // $pickup = $voucher->pickup;
            $this->voucherRepository->destroy($voucher);

            return response()->json(['status' => 1], Response::HTTP_OK);
            // 'total_prepaid_amount' => $pickup->vouchers()->prepaidAmount()
        }
        return response()->json(['status' => 2, 'message' => "Unauthenticated!"], Response::HTTP_OK);
    }

    public function update_status(Request $request, Voucher $voucher)
    {
        $voucher = $this->voucherRepository->update_status($voucher, $request->all());
        return new VoucherResource($voucher->load([
            'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
            'call_status', 'delivery_status', 'delegate_duration', 'delegate_person', 'store_status', 'parcels', 'pickup', 'pickup.sender', 'pickup.opened_by_staff' => function ($query) {
                $query->withTrashed();
            }
        ]));
    }

    public function draftVouchers()
    {
        $vouchers = Voucher::where('created_by_id', auth()->user()->id)
            ->where('created_by_type', 'Merchant')
            ->where('pickup_id', null)
            ->orderBy('id', 'desc')
            ->get();

        return new VoucherCollection($vouchers->load([
            'pickup', 'pickup.sender', 'customer', 'payment_type',
            'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
            'call_status', 'delivery_status', 'store_status', 'payment_status', 'delegate_duration',
            'delegate_person', 'parcels','pending_returning_actor','order' => function ($query) {
                $query->withTrashed();
            }
        ]));
    }

    public function bindedVouchers()
    {
        $vouchers = Voucher::where('created_by_id', auth()->user()->id)
            ->where('created_by_type', 'Merchant')
            // ->whereNotNull('qr_associat_id')
            ->whereNotNull('pickup_id')
            ->orderBy('id', 'desc')
            ->get();

        return new VoucherCollection($vouchers->load([
            'pickup', 'pickup.sender', 'customer', 'payment_type',
            'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
            'call_status', 'delivery_status', 'store_status', 'payment_status', 'delegate_duration',
            'delegate_person', 'parcels','pending_returning_actor','order' => function ($query) {
                $query->withTrashed();
            }
        ]));
    }

    public function deliveringVouchers()
    {
        if (request()->has('export')) {
            $filename = 'merchant_delivering_vouchers.xlsx';
            Excel::store(new MerchantDeliveringVoucherData, $filename, 'public', null, [
                    'visibility' => 'public',
            ]);
            $file = storage_path('/app/public/merchant_delivering_vouchers.xlsx');
            return response()->download($file)->deleteFileAfterSend();
        }
        
        $vouchers = Voucher::with([
            'pickup', 'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'pickup.sender.staff',
            'call_status', 'delivery_status', 'store_status', 'payment_status', 'delegate_duration', 
            'delegate_person', 'parcels','pending_returning_actor','order' => function ($query) {
                $query->withTrashed();
            }
        ])
            // ->where('created_by_id', auth()->user()->id)
            // ->where('created_by_type', 'Merchant')
            // ->whereNotIn('delivery_status_id', [8, 9])
            ->whereIn('delivery_status_id', [1, 2])
            ->whereHas('pickup', function ($qr) {
                $qr->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
            ->filter(request()->only([
                'voucher_invoice', 'date', 'receiver_city', 'receiver_zone', 'sender', 'receiver',
                'call_status', 'delivery_status', 'receiver_name', 'receiver_phone', 'delivered_date',
                'start_date', 'end_date', 'from_city_id', 'to_city_id', 'pickup_date', 'pickup_start_date',
                'pickup_end_date'
            ]))
            ->order(request()->only([
                'sortBy', 'orderBy'
            ]));

        if (request()->has('paginate')) {
            $vouchers = $vouchers->paginate(request()->get('paginate'));
        } else {
            $vouchers = $vouchers->get();
        }

        return new VoucherCollection($vouchers);
    }

    public function attemptVouchers()
    {
        $vouchers = Voucher::with([
            'pickup', 'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'pickup.sender.staff',
            'call_status', 'delivery_status', 'store_status', 'payment_status', 'delegate_duration', 
            'delegate_person', 'parcels','pending_returning_actor','order' => function ($query) {
                $query->withTrashed();
            }
        ])
            // ->where('created_by_id', auth()->user()->id)
            // ->where('created_by_type', 'Merchant')
            ->whereIn('delivery_status_id', [2, 3, 4])
            ->whereHas('pickup', function ($qr) {
                $qr->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
            ->filter(request()->only([
                'voucher_invoice', 'date', 'receiver_city', 'receiver_zone', 'sender', 'receiver',
                'call_status', 'delivery_status', 'receiver_name', 'receiver_phone', 'delivered_date',
                'start_date', 'end_date', 'from_city_id', 'to_city_id', 'pickup_date'
            ]))
            ->order(request()->only([
                'sortBy', 'orderBy'
            ]));

        if (request()->has('paginate')) {
            $vouchers = $vouchers->paginate(request()->get('paginate'));
        } else {
            $vouchers = $vouchers->get();
        }

        return new VoucherCollection($vouchers);
    }

    public function deliveredVouchers()
    {
        $vouchers = Voucher::with([
            'pickup', 'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'pickup.sender.staff',
            'call_status', 'delivery_status', 'store_status', 'payment_status', 
            'delegate_duration', 'delegate_person', 'parcels','pending_returning_actor','order'=> function ($query) {
                $query->withTrashed();
            }
        ])
            // ->where('created_by_id', auth()->user()->id)
            // ->where('created_by_type', 'Merchant')
            ->where('delivery_status_id', 8)
            ->whereHas('pickup', function ($qr) {
                $qr->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
            ->filter(request()->only([
                'voucher_invoice', 'date', 'receiver_city', 'receiver_zone', 'sender', 'receiver',
                'call_status', 'delivery_status', 'receiver_name', 'receiver_phone', 'delivered_date',
                'start_date', 'end_date', 'from_city_id', 'to_city_id', 'pickup_date'
            ]))
            ->orderBy('delivered_date', 'desc');
            // ->order(request()->only([
            //     'sortBy', 'orderBy'
            // ]));

        if (request()->has('paginate')) {
            $vouchers = $vouchers->paginate(request()->get('paginate'));
        } else {
            $vouchers = $vouchers->get();
        }

        return new VoucherCollection($vouchers);
    }

    public function pendingReturnVouchers() {
        $vouchers = Voucher::with([
            'pickup', 'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'pickup.sender.staff',
            'call_status', 'delivery_status', 'store_status', 'payment_status', 
            'delegate_duration', 'delegate_person', 'parcels','pending_returning_actor','order' => function ($query) {
                $query->withTrashed();
            }
        ])
            ->where('is_return', false)
            ->where('delivery_status_id', 9)
            ->whereHas('pickup', function ($qr) {
                // ->where('sender_type', 'Merchant')
                $qr->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
            ->whereDoesntHave('return_sheets')
            ->filter(request()->only([
                'voucher_invoice', 'date', 'receiver_city', 'receiver_zone', 'sender', 'receiver',
                'call_status', 'delivery_status', 'receiver_name', 'receiver_phone', 'delivered_date',
                'start_date', 'end_date', 'from_city_id', 'to_city_id', 'pickup_date'
            ]))
            ->order(request()->only([
                'sortBy', 'orderBy'
            ]));

            if (request()->has('paginate')) {
                $vouchers = $vouchers->paginate(request()->get('paginate'));
            } else {
                $vouchers = $vouchers->get();
            }
    
            return new VoucherCollection($vouchers);
    }

    public function returningVouchers()
    {
        $vouchers = Voucher::with([
            'pickup', 'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'pickup.sender.staff',
            'call_status', 'delivery_status', 'store_status', 'payment_status', 'delegate_duration', 
            'delegate_person', 'parcels','pending_returning_actor','order' => function ($query) {
                $query->withTrashed();
            }
        ])
            // ->where('created_by_id', auth()->user()->id)
            // ->where('created_by_type', 'Merchant')
            ->where('is_return', false)
            ->whereHas('return_sheets', function ($qr) {
                $qr->where('is_returned', false);
            })
            ->whereHas('pickup', function ($qr) {
                $qr->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
            ->filter(request()->only([
                'voucher_invoice', 'date', 'receiver_city', 'receiver_zone', 'sender', 'receiver',
                'call_status', 'delivery_status', 'receiver_name', 'receiver_phone', 'delivered_date',
                'start_date', 'end_date', 'from_city_id', 'to_city_id', 'pickup_date'
            ]))
            ->order(request()->only([
                'sortBy', 'orderBy'
            ]));

        if (request()->has('paginate')) {
            $vouchers = $vouchers->paginate(request()->get('paginate'));
        } else {
            $vouchers = $vouchers->get();
        }

        return new VoucherCollection($vouchers);
    }

    public function returnedVouchers() {
        $vouchers = Voucher::with([
            'pickup', 'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'pickup.sender.staff',
            'call_status', 'delivery_status', 'store_status', 'payment_status', 'delegate_duration',
             'delegate_person', 'parcels','pending_returning_actor','order' => function ($query) {
                $query->withTrashed();
            }
        ])
            ->where('is_return', true)
            ->whereHas('pickup', function ($qr) {
                // ->where('sender_type', 'Merchant')
                $qr->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
            ->whereHas('return_sheets', function ($qr) {
                $qr->where('is_returned', true);
            })
            ->filter(request()->only([
                'voucher_invoice', 'date', 'receiver_city', 'receiver_zone', 'sender', 'receiver',
                'call_status', 'delivery_status', 'receiver_name', 'receiver_phone', 'delivered_date',
                'start_date', 'end_date', 'from_city_id', 'to_city_id', 'pickup_date'
            ]))
            ->order(request()->only([
                'sortBy', 'orderBy'
            ]));

            if (request()->has('paginate')) {
                $vouchers = $vouchers->paginate(request()->get('paginate'));
            } else {
                $vouchers = $vouchers->get();
            }
    
            return new VoucherCollection($vouchers);
    }

    public function cannotDeliveredVouchers() {
        $vouchers = Voucher::with([
            'pickup', 'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'pickup.sender.staff',
            'call_status', 'delivery_status', 'store_status', 'payment_status', 'delegate_duration',
             'delegate_person', 'parcels','pending_returning_actor','order' => function ($query) {
                $query->withTrashed();
            }
        ])
            ->whereNotIn('delivery_status_id', [1, 2, 8, 9])
            ->whereHas('pickup', function ($qr) {
                // ->where('sender_type', 'Merchant')
                $qr->where('sender_type', 'Merchant')
                    ->where('sender_id', auth()->user()->id);
            })
            ->filter(request()->only([
                'voucher_invoice', 'date', 'receiver_city', 'receiver_zone', 'sender', 'receiver',
                'call_status', 'delivery_status', 'receiver_name', 'receiver_phone', 'delivered_date',
                'start_date', 'end_date', 'from_city_id', 'to_city_id', 'pickup_date'
            ]))
            ->order(request()->only([
                'sortBy', 'orderBy'
            ]));

            if (request()->has('paginate')) {
                $vouchers = $vouchers->paginate(request()->get('paginate'));
            } else {
                $vouchers = $vouchers->get();
            }
    
            return new VoucherCollection($vouchers);
    }

    public function deliveredReturningVouchers()
    {
        if (request()->has('export')) {
            $filename = 'merchant_delivered_returning_vouchers.xlsx';
            Excel::store(new MerchantDeliveredReturningVoucherData, $filename, 'public', null, [
                    'visibility' => 'public',
            ]);
            $file = storage_path('/app/public/merchant_delivered_returning_vouchers.xlsx');
            return response()->download($file)->deleteFileAfterSend();
        }
        
        $vouchers = Voucher::with([
            'pickup', 'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate', 'pickup.sender.staff',
            'call_status', 'delivery_status', 'store_status', 'payment_status', 'delegate_duration', 
            'delegate_person', 'parcels','pending_returning_actor','order' => function ($query) {
                $query->withTrashed();
            }
        ])
            // ->where('created_by_id', auth()->user()->id)
            // ->where('created_by_type', 'Merchant')
            ->where(function ($qr) {
                $qr->where('delivery_status_id', 8) //delivered vouchers
                    ->whereHas('pickup', function ($q) {
                        $q->where('sender_type', 'Merchant')
                            ->where('sender_id', auth()->user()->id);
                    });
            })
            ->orWhere(function ($qr) {
                $qr->where('is_return', false)
                    ->where('delivery_status_id', 9)
                    // ->whereHas('return_sheets', function ($qr) {
                    //     $qr->where('is_returned', true);
                    // }) //returned vouchers
                    ->has('return_sheets', '<', 1) // not has return sheets
                    ->whereHas('pickup', function ($q) {
                        $q->where('sender_type', 'Merchant')
                            ->where('sender_id', auth()->user()->id);
                    });
                // ->whereHas('return_sheets', function ($q) {
                //     $q->where('is_returned', false);
                // });
            }) // returning vouchers
            ->filter(request()->only([
                'voucher_invoice', 'date', 'receiver_city', 'receiver_zone', 'sender', 'receiver',
                'call_status', 'delivery_status', 'receiver_name', 'receiver_phone', 'delivered_date',
                'start_date', 'end_date', 'from_city_id', 'to_city_id', 'pickup_date', 'pickup_start_date',
                'pickup_end_date', 'delivered_start_date', 'delivered_end_date'
            ]))
            ->order(request()->only([
                'sortBy', 'orderBy'
            ]));

        if (request()->has('paginate')) {
            $vouchers = $vouchers->paginate(request()->get('paginate'));
        } else {
            $vouchers = $vouchers->get();
        }

        return new VoucherCollection($vouchers);
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
            // $sun_total = 'Add Delivery Fee';
            // $net_total = 'Delivery Fee Included';
            // $delivery_only = 'Only Collect Delivery Fee';
            // $ntc = 'Nothing To Collect';
            // $prepaid_ntc = 'Delivery Fee Prepaid Nothing To Collect';
            // $prepaid_collect = 'Delivery Prepaid Cash Collection Required';
            if (isset($data['payment_type'])) {
                $payment_type_id = PaymentType::where('name', $data['payment_type'])->first()->id;
            } else {
                $payment_type_id = PaymentType::where('name', 'Delivery Fee Included')->first()->id;
            }
            // $payment_type = isset($data['payment_type']) ? $data['payment_type'] : 'Net total';
            // $payment_type_id = PaymentType::where('name', $payment_type)->first()->id;
            $data['receiver_city_id'] = $receiver_city->id;
            $data['receiver_zone_id'] = $receiver_zone->id;
            $data['payment_type_id'] = $payment_type_id;
            $data['store_status_id'] = 1;
            $data['sender_id'] = auth()->user()->id;
            $data['platform'] = 'Merchant Dashboard';
            
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
     * Upload voucher's image
     */
    public function upload(Voucher $voucher, FileRequest $request)
    {
        if ($request->hasFile('file') && $file = $request->file('file')) {
            $attachment = $this->voucherRepository->upload($voucher, $file);
            return new AttachmentResource($attachment);
        }
        return response()->json(['status' => 2, 'message' => 'Incorect route'], Response::HTTP_OK);
        // return new VoucherResource($voucher->load([
        //     'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
        //     'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate',
        //     'call_status', 'delivery_status', 'delegate_duration', 'delegate_person', 'store_status', 'parcels', 'pickup', 'pickup.sender', 'pickup.opened_by_staff' => function ($query) {
        //         $query->withTrashed();
        //     }, 'attachments'
        // ]));
    }
}
