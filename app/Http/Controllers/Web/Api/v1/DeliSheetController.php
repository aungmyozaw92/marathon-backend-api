<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Staff;
use App\Models\Pickup;
use App\Models\Voucher;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\DeliSheet;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\DeliSheetVoucher;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attachment\FileRequest;
use App\Http\Resources\Pickup\PickupCollection;
use App\Http\Resources\Voucher\VoucherResource;
use App\Http\Requests\DeliSheet\AddVoucherRequest;
use App\Http\Resources\DeliSheet\DeliSheetResource;
use App\Repositories\Web\Api\v1\DeliSheetRepository;
use App\Http\Requests\DeliSheet\RemoveVoucherRequest;
use App\Http\Resources\Attachment\AttachmentResource;
use App\Http\Resources\DeliSheet\DeliSheetCollection;
use App\Repositories\Web\Api\v1\AttachmentRepository;
use App\Http\Requests\DeliSheet\ChangeDeliveryRequest;
use App\Http\Requests\DeliSheet\CreateDeliSheetRequest;
use App\Http\Requests\DeliSheet\UpdateDeliSheetRequest;
use App\Http\Resources\PickupPayment\PickupPaymentCollection;

class DeliSheetController extends Controller
{
    /**
     * @var DeliSheetRepository
     */
    protected $deliSheetRepository;
    protected $attachmentRepository;

    /**
     * DeliSheetController constructor.
     *
     * @param DeliSheetRepository $deliSheetRepository
     */
    public function __construct(DeliSheetRepository $deliSheetRepository, AttachmentRepository $attachmentRepository)
    {
        $this->deliSheetRepository = $deliSheetRepository;
        $this->attachmentRepository = $attachmentRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $city_id = auth()->user()->city_id;
        $deliSheets = DeliSheet::filter(request()->only([
                        'date', 'start_date', 'end_date', 'delivery_id', 'is_closed', 'is_paid','delisheet_invoice_no', 'delisheet_invoice'
                    ]))
                    ->with('zone', 'delivery', 'staff')
                    ->withCount('vouchers')
                    ->where(function ($qr) use ($city_id) {
                        $qr->whereHas('delivery', function ($q) use ($city_id) {
                            $q->where('city_id', $city_id);
                        })
                            ->orWhereHas('staff', function ($q) use ($city_id) {
                                $q->where('city_id', $city_id);
                            });
                    })
                    // ->where(function ($qr) use ($city_id) {
                    //     $qr->whereHas('vouchers', function ($query) {
                    //         $query->where('origin_city_id', auth()->user()->city_id);
                    //     })
                    //         ->orWhereDoesntHave('vouchers');
                    // })
                    ->orderBy('id', 'desc');

        if (request()->has('paginate')) {
            $deliSheets = $deliSheets->paginate(request()->get('paginate'));
        } else {
            $deliSheets = $deliSheets->get();
        }

        return new DeliSheetCollection($deliSheets);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateDeliSheetRequest $request)
    {
        if ($request->get('vouchers')) {
            $request->merge([
                'vouchers' => array_unique($request->get('vouchers'), SORT_REGULAR)
            ]);

            foreach ($request->get('vouchers') as $voucher) {
                $voucher_exists = DeliSheetVoucher::whereVoucherId($voucher['id'])->exists();
                $voucher = Voucher::findOrFail($voucher['id']);
                if ($voucher_exists && !is_null($voucher->outgoing_status)) {
                    return response()->json([
                        'status' => 4, 'message' => 'Voucher is already assigned to DeliSheet.'
                    ], Response::HTTP_OK);
                }
            }
        }

        $deliSheet = $this->deliSheetRepository->create($request->all());

        return new DeliSheetResource($deliSheet->load(['zone', 'delivery', 'staff']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DeliSheet  $deliSheet
     * @return \Illuminate\Http\Response
     */
    public function show(DeliSheet $deliSheet)
    {
        // 'vouchers.pickup.sender.staff'
        return new DeliSheetResource($deliSheet->load([
            'zone', 'delivery', 'staff', 
            'vouchers', 
            'vouchers.pickup', 
            'vouchers.pickup.sender',
            'vouchers.customer', 
            'vouchers.receiver_city', 
            'vouchers.receiver_zone', 
            'vouchers.call_status',
            'vouchers.delivery_status', 
            'vouchers.store_status', 
            'vouchers.payment_type', 
            'vouchers.attachments',
            'vouchers.pickup.sender.staff', 
            // 'vouchers.pickup.sender_associate.phones', 
            // 'vouchers.pickup.sender_associate.contact_associates' => function ($query) {
            //     $query->selectRaw("value as phone")->where('type','phone');
            // }, 
            'attachments'
        ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DeliSheet  $deliSheet
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDeliSheetRequest $request, DeliSheet $deliSheet)
    {
        if (!$deliSheet->is_closed) {
            $deliSheet = $this->deliSheetRepository->update($deliSheet, $request->all());

            return new DeliSheetResource($deliSheet->load(['zone', 'delivery', 'staff']));
        }

        return response()->json([
            'status' => 2, 'message' => 'DeliSheet is already closed'
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DeliSheet  $deliSheet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, DeliSheet $deliSheet)
    {
        if ($deliSheet->is_closed) {
            return response()->json([
                        'status' => 2, 'message' => 'Cannot delete because this deliSheet is already closed'
                    ], Response::HTTP_OK);
        }

        $voucher_count = $deliSheet->deli_sheet_vouchers->count();

        if ($voucher_count > 0) {
            return response()->json([
                        'status' => 2, 'message' => 'Cannot delete because this deliSheet have '   .$voucher_count. ' voucher'
                    ], Response::HTTP_OK);
        }

        $this->deliSheetRepository->destroy($deliSheet);

        return response()->json(['status' => 1 ,'message' => 'Successfully updated'
                    ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DeliSheet  $deliSheet
     * @return \Illuminate\Http\Response
     */
    public function delivery(Staff $delivery)
    {
        return new DeliSheetCollection($delivery->deli_sheets->load([
            'zone', 'delivery', 'staff', 'vouchers', 'vouchers.pickup', 'vouchers.pickup.sender',
            'vouchers.customer', 'vouchers.receiver_city', 'vouchers.receiver_zone', 'vouchers.call_status',
            'vouchers.delivery_status', 'vouchers.store_status', 'vouchers.payment_type', 'attachments'
        ]));
    }

    public function change_delivery(ChangeDeliveryRequest $request)
    {
        $deliSheet = DeliSheet::find($request->get('deli_sheet_id'));
        if (!$deliSheet->is_closed) {
            $deliSheet = $this->deliSheetRepository->change_delivery($deliSheet, $request->all());

            return new DeliSheetResource($deliSheet->load([
                'zone', 'delivery', 'staff', 'vouchers', 'vouchers.pickup', 'vouchers.pickup.sender',
                'vouchers.customer', 'vouchers.receiver_city', 'vouchers.receiver_zone', 'vouchers.call_status',
                'vouchers.delivery_status', 'vouchers.store_status', 'vouchers.payment_type', 'vouchers.attachments',
                'attachments'
            ]));
            // return response()->json([
            //     'status' => 1, 'message' => 'Delivery has changed successfully.'
            // ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => 2, 'message' => 'Cannot change Delivery because deliSheet is already closed.'
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DeliSheet  $deliSheet
     * @return \Illuminate\Http\Response
     */
    public function deliveryPickups()
    {
        // $customers = Customer::whereHas('city', function ($q) {
        //     $q->where('city_id', getBranchCityId());
        // })->get()->pluck('id');
        // $merchants = Merchant::whereHas('merchant_associates', function ($q) {
        //     $q->where('city_id', getBranchCityId());
        // })->get()->pluck('id');

        $pickups = Pickup::with('sender', 'opened_by_staff', 'created_by', 'pickuped_by')
            ->filter(request()->only(['date', 'start_date', 'end_date', 'pickuped_by_id', 'pickuped_by_type', 'is_closed', 'is_paid']))
            ->whereNotNull('pickuped_by_id')
            ->whereNotNull('pickuped_by_type')
            ->where('city_id', auth()->user()->city_id)
            // ->where(function ($qr) use ($customers, $merchants) {
            //     $qr->where('sender_type', 'Customer')->whereIn('sender_id', $customers)
            //         ->orWhere('sender_type', 'Merchant')->whereIn('sender_id', $merchants);
            // })
            // ->whereNull('closed')
            // ->where('is_closed', 1)
            // ->where('is_paid', 0)
            ->orderBy('id', 'desc');
        
        if (request()->has('paginate')) {
            // dd(request()->get('paginate'));
            $pickups = $pickups->paginate(request()->get('paginate'));
        // $deliSheets->paginate(request()->get('paginate'));
        } else {
            $pickups = $pickups->get();
        }

        // $customers = Customer::whereHas('city', function ($q) {
        //     $q->where('city_id', getBranchCityId());
        // })->get()->pluck('id');
        // $merchants = Merchant::whereHas('merchant_associates', function ($q) {
        //     $q->where('city_id', getBranchCityId());
        // })->get()->pluck('id');
        // $senders = $customers->merge($merchants);


        // $pickups_customer = $pickups->where('sender_type', 'Customer')->whereIn('sender.id', $customers);
        // $pickups_merchant = $pickups->where('sender_type', 'Merchant')->whereIn('sender.id', $merchants);
        // $pickups = $pickups_customer->merge($pickups_merchant);
        // $pickups = $pickups->whereIn('sender.id', $senders);

        // return new PickupCollection($delivery->pickups->load([
        //     'sender', 'opened_by_staff'
        // ]));
        return new PickupPaymentCollection($pickups);
    }

    public function removeVouchers(RemoveVoucherRequest $request, DeliSheet $deliSheet)
    {
        if (!$deliSheet->is_closed) {
            $deliSheet = $this->deliSheetRepository->remove_vouchers($deliSheet, $request->all());

            return response()->json([
                'status' => 1, 'message' => 'Voucher has been successfully removed.'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => 2, 'message' => 'Cannot remove because deliSheet is already closed'
        ], Response::HTTP_OK);
    }
    
    public function addVouchers(AddVoucherRequest $request, DeliSheet $deliSheet)
    {
        if (!$deliSheet->is_closed) {
            foreach ($request->get('vouchers') as $voucher) {
                $voucher_exists = DeliSheetVoucher::whereVoucherId($voucher['id'])->exists();
                $voucher = Voucher::findOrFail($voucher['id']);

                if ($voucher->delivery_status_id == 8 || $voucher->delivery_status_id == 9) {
                    return response()->json([
                        'status' => 2, 'message' => 'Voucher is already returned or delivered.'
                    ], Response::HTTP_OK);
                }
                if ($voucher_exists && !is_null($voucher->outgoing_status)) {
                    return response()->json([
                        'status' => 2, 'message' => 'Voucher is already assigned to DeliSheet.'
                    ], Response::HTTP_OK);
                }
            }
            $deliSheet = $this->deliSheetRepository->add_vouchers($deliSheet, $request->all());

            return response()->json([
                'status' => 1, 'message' => 'Voucher has been successfully added.'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => 2, 'message' => 'Cannot add new voucher because deliSheet is already closed'
        ], Response::HTTP_OK);
    }

    public function addScanVouchers(DeliSheet $deliSheet)
    {
        if (!$deliSheet->is_closed) {
            $voucher = $this->getVoucher(request()->get('invoice_no'));
            $store_statuses = array(2,3,4,8);
            if ($voucher) {
                if ($this->assignSheet($voucher) != null) {
                    return response()->json([
                        'status' => 2, 'message' => 'Voucher is already assigned.'
                    ], Response::HTTP_OK);
                } elseif (!in_array($voucher->store_status_id, $store_statuses)) {
                    return response()->json([
                        'status' => 2, 'message' => 'Store Status is invalid.'
                    ], Response::HTTP_OK);
                } elseif ($voucher->receiver_city_id != auth()->user()->city_id) {
                    return response()->json([
                        'status' => 2, 'message' => 'This voucher is for Waybill.'
                    ], Response::HTTP_OK);
                } elseif ($voucher->delivery_status_id == 8 || $voucher->delivery_status_id == 9) {
                    return response()->json([
                        'status' => 2, 'message' => 'Voucher is already returned or delivered.'
                    ], Response::HTTP_OK);
                } elseif ($voucher->postpone_date && $voucher->postpone_date->greaterThan(\Carbon\Carbon::now())) {
                    return response()->json([
                        'status' => 2, 'message' => "Voucher is postponed to {$voucher->postpone_date->format('Y-m-d')} Date"
                    ], Response::HTTP_OK);
                }
            } else {
                return response()->json([
                    'status' => 2, 'message' => 'Voucher does not exit'
                ], Response::HTTP_OK);
            }

            $voucher_exists = DeliSheetVoucher::whereVoucherId($voucher->id)->exists();

            if ($voucher_exists && !is_null($voucher->outgoing_status)) {
                return response()->json([
                    'status' => 2, 'message' => 'Voucher is already assigned to DeliSheet.'
                ], Response::HTTP_OK);
            }
            $deliSheet = $this->deliSheetRepository->add_scan_vouchers($deliSheet, $voucher);

            return response()->json([
                'status' => 1,
                'data'   => new VoucherResource($voucher),
                'message' => 'Voucher has been successfully added.'
            ], Response::HTTP_OK);
        }
    
        return response()->json([
            'status' => 2, 'message' => 'Cannot add new voucher because deliSheet is already closed'
        ], Response::HTTP_OK);
    }

    public function addExpressScanVouchers(DeliSheet $deliSheet)
    {
        return response()->json([
            'status' => 2, 'message' => 'This service temporary close'
        ], Response::HTTP_OK);
        if (!$deliSheet->is_closed) {
            $voucher = $this->getVoucher(request()->get('invoice_no'));
            // dd($voucher->receiver_city_id != auth()->user()->city_id);
            if ($voucher) {
                if ($this->assignSheet($voucher) != null) {
                    return response()->json([
                        'status' => 2, 'message' => 'Voucher is already assigned.'
                    ], Response::HTTP_OK);
                } elseif ($voucher->receiver_city_id != auth()->user()->city_id) {
                    return response()->json([
                        'status' => 2, 'message' => 'This voucher is for Waybill.'
                    ], Response::HTTP_OK);
                } elseif ($voucher->delivery_status_id == 8 || $voucher->delivery_status_id == 9) {
                    return response()->json([
                        'status' => 2, 'message' => 'Voucher is already returned or delivered.'
                    ], Response::HTTP_OK);
                } elseif ($voucher->postpone_date && $voucher->postpone_date->greaterThan(\Carbon\Carbon::now())) {
                    return response()->json([
                        'status' => 2, 'message' => "Voucher is postponed to {$voucher->postpone_date->format('Y-m-d')} Date"
                    ], Response::HTTP_OK);
                }
            } else {
                return response()->json([
                    'status' => 2, 'message' => 'Voucher does not exit'
                ], Response::HTTP_OK);
            }

            $voucher_exists = DeliSheetVoucher::whereVoucherId($voucher->id)->exists();

            if ($voucher_exists && !is_null($voucher->outgoing_status)) {
                return response()->json([
                    'status' => 2, 'message' => 'Voucher is already assigned to DeliSheet.'
                ], Response::HTTP_OK);
            }
            $deliSheet = $this->deliSheetRepository->add_scan_vouchers($deliSheet, $voucher);

            return response()->json([
                'status' => 1,
                'data'   => new VoucherResource($voucher),
                'message' => 'Voucher has been successfully added.'
            ], Response::HTTP_OK);
        }
    
        return response()->json([
            'status' => 2, 'message' => 'Cannot add new voucher because deliSheet is already closed'
        ], Response::HTTP_OK);
    }

    protected function getVoucher($voucher_no)
    {
        $agent_city_id = request()->get('agent_city_id');
        
        $vouchers = Voucher::with([
                    'pickup','pickup.sender.staff', 'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
                    'call_status', 'delivery_status', 'store_status', 'payment_status','pending_returning_actor' => function ($query) {
                        $query->withTrashed();
                    },'attachments'
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

        //if (Str::length($voucher_no) > 7) {
        $no = substr($voucher_no, 0, 2);
        if ($no != 'VN') {
            $voucher = $vouchers->where('thirdparty_invoice', $voucher_no)->first();
        } else {
            $voucher = $vouchers->where('voucher_invoice', $voucher_no)->first();
        }

        return $voucher;
    }

    protected function assignSheet($voucher)
    {
        if ($voucher->outgoing_status === 0) {
            return $voucher->delisheets()->latest()->first();
        } elseif ($voucher->outgoing_status === 1) {
            return $voucher->waybills()->latest()->first();
        } elseif ($voucher->outgoing_status === 2) {
            return $voucher->bussheets()->latest()->first();
        } elseif ($voucher->outgoing_status === 3) {
            return "Merchant Sheet Draft";
        } elseif ($voucher->outgoing_status === 4) {
            return $voucher->merchant_sheets()->latest()->first();
        } elseif ($voucher->outgoing_status === 5) {
            return $voucher->return_sheets()->latest()->first();
        }
    }

    /**
     * Store a newly uploaded image in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(DeliSheet $deliSheet, FileRequest $request)
    {
        $attachment = $this->attachmentRepository->create_deliSheet_attachmet($deliSheet, $request->all());
        return new AttachmentResource($attachment);
    }

    public function generateToken(DeliSheet $deliSheet)
    {
        if (!$deliSheet->is_closed) {
            return response()->json([
                'status' => 2, 'message' => 'DeliSheet need to close first.'
            ], Response::HTTP_OK);
        }
        if ($deliSheet->is_paid) {
            return response()->json([
                'status' => 2, 'message' => 'DeliSheet is already paid.'
            ], Response::HTTP_OK);
        }
        if ($deliSheet->payment_token) {
            return response()->json([
                'status' => 2, 'message' => 'Delisheet already has token.'
            ], Response::HTTP_OK);
        }

        $token = Str::random(32);
        $deliSheet->payment_token = $token;
        $deliSheet->save();
        $deliSheet->refresh();

        return response()->json([
                'status' => 1, 
                'token' => $token, 
                'message' => 'Success'
            ], Response::HTTP_OK);
    }
}

