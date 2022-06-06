<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\City;
use App\Models\Staff;
use App\Models\Journal;
use App\Models\Voucher;
use App\Models\Waybill;
use App\Models\BusStation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\WaybillVoucher;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attachment\FileRequest;
use App\Http\Resources\Voucher\VoucherResource;
use App\Http\Resources\Waybill\WaybillResource;
use App\Http\Resources\Waybill\WaybillCollection;
use App\Http\Requests\DeliSheet\AddVoucherRequest;
use App\Repositories\Web\Api\v1\WaybillRepository;
use App\Http\Requests\Waybill\CreateWaybillRequest;
use App\Http\Requests\Waybill\UpdateWaybillRequest;
use App\Http\Requests\Waybill\ConfirmWaybillRequest;
use App\Http\Requests\DeliSheet\RemoveVoucherRequest;
use App\Http\Resources\Attachment\AttachmentResource;
use App\Repositories\Web\Api\v1\AttachmentRepository;

class WaybillController extends Controller
{
    /**
     * @var WaybillRepository
     */
    protected $waybillRepository;
    protected $attachmentRepository;

    /**
     * WaybillController constructor.
     *
     * @param WaybillRepository $waybillRepository
     */
    public function __construct(WaybillRepository $waybillRepository, AttachmentRepository $attachmentRepository)
    {
        $this->waybillRepository = $waybillRepository;
        $this->attachmentRepository = $attachmentRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $waybills = Waybill::filter(request()->only([
            'date', 'start_date', 'end_date', 'delivery_id', 'waybill_invoice'
        ]))->get();

        return new WaybillCollection($waybills->load(['vouchers', 'delivery', 'staff', 'city','from_city', 'to_city','from_agent','to_agent']));
    }

    public function incomingWaybills()
    {
        $waybills = Waybill::where('to_city_id', auth()->user()->city_id)
            ->where('is_closed', 1)
            // ->where(function ($qr) {
            //     request()->get('start_date') ? $qr : $qr->where('is_received', 0);
            // })
            ->with('delivery', 'staff', 'city', 'from_city', 'to_city', 'to_bus_station', 'gate','from_agent', 'to_agent')
            ->withCount('vouchers')
            ->filter(request()->only([
                'date', 'start_date', 'end_date', 'delivery_id', 'from_city_id', 
                'is_closed', 'is_paid', 'waybill_invoice','is_received'
            ]))
            ->orderBy('id', 'desc');

        if (request()->has('paginate')) {
            $waybills = $waybills->paginate(request()->get('paginate'));
        } else {
            $waybills = $waybills->get();
        }

        return new WaybillCollection($waybills);
    }

    public function outgoingWaybills()
    {
        // $agent_city_id = request()->get('agent_city_id');
        // if ($agent_city_id) {
        //     $city_id = $agent_city_id;
        // } else {
        //     $city_id = auth()->user()->city_id;
        // }

        $waybills = Waybill::where(function ($query) {
            if (auth()->user()->hasRole('Agent')) {
                request()->get('agent_city_id') ? $query->where('from_city_id', request()->get('agent_city_id')) : $query;
            } else {
                $query->where('from_city_id', auth()->user()->city_id);
            }
        })
            ->filter(request()->only([
                'date', 'start_date', 'end_date', 'delivery_id', 'to_city_id', 
                'is_closed', 'is_paid', 'waybill_invoice','is_received'
            ]))
            ->with('delivery', 'staff', 'city', 'from_city', 'to_city','from_agent', 'to_agent')
            ->withCount('vouchers')
            ->orderBy('id', 'desc');

        if (request()->has('paginate')) {
            $waybills = $waybills->paginate(request()->get('paginate'));
        } else {
            $waybills = $waybills->get();
        }

        return new WaybillCollection($waybills);
    }

    public function allAgentWaybills()
    {
        $waybills = Waybill::where(function ($query) {
            if (request()->has('from_to_city_id')) {
                $query->where('from_city_id', request()->get('from_to_city_id'))
                    ->orWhere('to_city_id', request()->get('from_to_city_id'));
            } elseif (request()->has('from_city_id')) {
                $query->where('from_city_id', request()->get('from_city_id'));
            } elseif (request()->has('to_city_id')) {
                $query->where('to_city_id', request()->get('to_city_id'));
            }
        })
            ->filter(request()->only([
                'date', 'start_date', 'end_date', 'delivery_id', 'to_city_id', 
                'is_closed', 'is_paid','from_agent_id','to_agent_id'
            ]))
            ->with('delivery', 'staff', 'city', 'from_city', 'to_city', 
                    'to_bus_station', 'gate','from_agent', 'to_agent')
            ->withCount('vouchers')
            ->orderBy('id', 'desc');

        if (request()->has('paginate')) {
            $waybills = $waybills->paginate(request()->get('paginate'));
        } else {
            $waybills = $waybills->get();
        }

        return new WaybillCollection($waybills);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateWaybillRequest $request)
    {
        $to_city = City::find($request->get('to_city_id'));

        if (!$to_city->branch) {
            // if (!$request->get('to_agent_id')) {
            //     return response()->json([
            //         'status' => 2, 'message' => 'Please select to agent '
            //     ], Response::HTTP_OK);
            // }
            
            $agent_account = $to_city->agent;
            if (!$agent_account) {
                return response()->json([
                    'status' => 2, 'message' => 'This city does not have an agent account'
                ], Response::HTTP_OK);
            }
            if (!$request->get('to_agent_id')) {
                $request['to_agent_id'] = $agent_account->id;
            }

        }

        $from_city_id = $request->get('from_city_id') ? $request->get('from_city_id') : BusStation::find($request->get('from_bus_station_id'))->city_id;
        if ($request->get('vouchers')) {
            $request->merge([
                'vouchers' => array_unique($request->get('vouchers'), SORT_REGULAR)
            ]);

            foreach ($request->get('vouchers') as $voucher) {
                $voucher_exists = WaybillVoucher::whereVoucherId($voucher['id'])->exists();
                $voucher = Voucher::findOrFail($voucher['id']);
                //dd($from_city_id);
                // if ($voucher->sender_city_id != $from_city_id || $voucher->receiver_city_id != $request->get('to_city_id')) {
                //     return response()->json([
                //         'status' => 2, 'message' =>'This ' .$voucher->voucher_invoice. ' destination do not match'
                //     ], Response::HTTP_OK);
                // }

                if ($voucher->delivery_status_id != 9) {
                    if ($voucher_exists && !is_null($voucher->outgoing_status)) {
                        return response()->json([
                            'status' => 4, 'message' => 'Voucher is already assigned to Waybill.'
                        ], Response::HTTP_OK);
                    }
                }
            }
        }
        $data = $request->all();
        $data['from_city_id'] = $from_city_id;

        $waybill = $this->waybillRepository->create($data);

        return new WaybillResource($waybill->load(['vouchers', 'delivery', 'staff','from_agent', 'to_agent']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Waybill  $waybill
     * @return \Illuminate\Http\Response
     */
    public function show(Waybill $waybill)
    {
        // 'vouchers.pickup.sender.staff'
        $waybill = Waybill::with([
            'from_bus_station',
            'to_bus_station',
            'gate',
            'from_city',
            'to_city',
            'from_city.agents',
            'to_city.agents',
            'from_agent', 
            'to_agent',
            'delivery',
            'staff',
            'vouchers',
            'vouchers.pickup',
            'vouchers.pickup.sender',
            'vouchers.customer',
            'vouchers.receiver_city',
            'vouchers.sender_city',
            'vouchers.receiver_zone',
            // 'vouchers.call_status',
            // 'vouchers.delivery_status',
            // 'vouchers.store_status',
            'vouchers.payment_type',
            'city',
            'attachments',
            'vouchers.pickup.sender.staff'
        ])->where('id', $waybill->id)->first();
        return new WaybillResource($waybill);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Waybill  $waybill
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateWaybillRequest $request, Waybill $waybill)
    {
        
        if ($waybill->is_confirm && ($waybill->from_agent_id != $request->from_agent_id || $waybill->to_agent_id != $request->to_agent_id)) {
            return response()->json(['status' => 2, "message" => "Cannot update agent coz of waybill is already confirm"]);
        }
        if(!$waybill->from_city->agent){
            if (($waybill->is_commissionable || $waybill->is_pointable) && 
                !$waybill->is_came_from_mobile && 
                $waybill->actby_mobile == null && 
                $request->is_closed) {
                return response()->json(['status' => 2, "message" => "Sorry!. Hero has not yet confirm delivering status."]);
            }
        }
        
        if (!$waybill->is_closed) {
            $waybill = $this->waybillRepository->update($waybill, $request->all());

            return new WaybillResource($waybill->load(['vouchers', 'delivery', 'staff','from_agent', 'to_agent']));
        }
    }

    public function agent_confirm(ConfirmWaybillRequest $request, Waybill $waybill)
    {
     
        if ($waybill->is_closed) {
            // Log::info("Agent Waybill Confirm Voucher in Controller");
            // foreach ($request->get('vouchers') as $voucher) {
            //     $voucher_exists = Journal::where('resourceable_id', $voucher['id'])->where('resourceable_type', 'Voucher')->exists();
            //     if ($voucher_exists) {
            //         return response()->json([
            //             'status' => 4, 'message' => 'Voucher is already closed in this Waybill.'
            //         ], Response::HTTP_OK);
            //     }
            // }
            if (!$waybill->is_received) {
                return response()->json([
                    'status' => 2, 'message' => '!Ops Waybill need to receive.'
                ], Response::HTTP_OK);
            }
            $voucher = Voucher::find($request->get('voucher_id'));
            if ($voucher->is_closed) {
                return response()->json([
                    'status' => 2, 'message' => 'Voucher is already closed in this Waybill.'
                ], Response::HTTP_OK);
            }

            $waybill = $this->waybillRepository->agent_confirm($waybill, $request->all());

            return new WaybillResource($waybill);
        }

        return response()->json([
            'status' => 2, 'message' => 'Waybill is still opening.Pls close'
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Waybill  $waybill
     * @return \Illuminate\Http\Response
     */
    public function destroy(Waybill $waybill)
    {
        if ($waybill->is_closed) {
            return response()->json([
                'status' => 2, 'message' => 'Cannot delete because this waybill is already closed'
            ], Response::HTTP_OK);
        }

        $voucher_count = $waybill->vouchers->count();
        if ($voucher_count > 0) {
            return response()->json([
                'status' => 2, 'message' => 'Cannot delete because this waybill have '   . $voucher_count . ' voucher'
            ], Response::HTTP_OK);
        }

        $this->waybillRepository->destroy($waybill);

        return response()->json([
            'status' => 1, 'message' => 'Successfully updated'
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
        return new WaybillCollection($delivery->waybills->load([
            'delivery', 'staff', 'vouchers', 'vouchers.pickup', 'vouchers.pickup.sender',
            'vouchers.customer', 'vouchers.receiver_city', 'vouchers.receiver_zone', 'vouchers.call_status',
            'vouchers.delivery_status', 'vouchers.store_status', 'vouchers.payment_type'
        ]));
    }

    public function removeVouchers(RemoveVoucherRequest $request, Waybill $waybill)
    {
        if ((!$waybill->is_closed || !$waybill->is_received) && !$waybill->is_confirm) {
            $waybill = $this->waybillRepository->remove_vouchers($waybill, $request->all());

            return response()->json([
                'status' => 1, 'message' => 'Ok successful.'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => 2, 'message' => 'Cannot remove because waybill is already closed'
        ], Response::HTTP_OK);
    }

    public function addVouchers(AddVoucherRequest $request, Waybill $waybill)
    {
        if ($waybill->is_closed || $waybill->is_received || $waybill->is_confirm) {
            return response()->json(
                ['status' => 2, 'message' => 'Cannot add new voucher because waybill is already closed and receiced'],
                Response::HTTP_OK
            );
        }

        $waybill = $this->waybillRepository->add_vouchers($waybill, $request->all());

        return response()->json([
            'status' => 1, 'message' => 'Voucher has been successfully added.'
        ], Response::HTTP_OK);
    }

    public function addScanVouchers(Waybill $waybill)
    {
        if ($waybill->is_closed || $waybill->is_received || $waybill->is_confirm) {
            return response()->json(
                ['status' => 2, 'message' => 'Cannot add new voucher because waybill is already closed and receiced'],
                Response::HTTP_OK
            );
        }

        $voucher = $this->getVoucher(request()->get('invoice_no'));
        // dd($voucher->receiver_city_id != auth()->user()->city_id);
        if ($voucher) {
            if ($this->assignSheet($voucher) != null) {
                return response()->json([
                    'status' => 2, 'message' => 'Voucher is already assigned.'
                ], Response::HTTP_OK);
            } elseif ($voucher->postpone_date && $voucher->postpone_date->greaterThan(\Carbon\Carbon::now())) {
                return response()->json([
                    'status' => 2, 'message' => "Voucher is postponed to {$voucher->postpone_date->format('Y-m-d')} Date"
                ], Response::HTTP_OK);
            } elseif ($voucher->receiver_city_id == $voucher->sender_city_id) {
                return response()->json([
                    'status' => 2, 'message' => 'This voucher not for waybill'
                ], Response::HTTP_OK);
            } elseif (!in_array($voucher->store_status_id, array(2,3,4,8))) {
                return response()->json([
                    'status' => 2, 'message' => 'Store Status is invalid.'
                ], Response::HTTP_OK);
            //    }elseif($voucher->receiver_city_id != $waybill->to_city_id){
        //         return response()->json([
        //             'status' => 2, 'message' => 'Please Choose the same receiver city with this waybill!'
        //         ], Response::HTTP_OK);
            } elseif ($voucher->delivery_status_id == 8) {
                return response()->json([
                    'status' => 2, 'message' => 'Voucher is already delivered.'
                ], Response::HTTP_OK);
            }elseif ($voucher->delivery_status_id == 9) {
                if($voucher->origin_city_id != auth()->user()->city_id){
                    return response()->json([
                        'status' => 2, 'message' => 'Voucher is already returned.'
                    ], Response::HTTP_OK);
                }
            }
            if (auth()->user()->city_id == 64 && $voucher->receiver_city_id != $waybill->to_city_id) {
                if($voucher->from_agent_id === $waybill->to_agent_id 
                    && !$voucher->is_return 
                    && !$voucher->is_closed
                    && $voucher->delivery_status_id === 9 ){
                       
                    }else{
                        return response()->json([
                            'status' => 2, 'message' => 'Please Choose the same receiver city with this waybill!'
                        ], Response::HTTP_OK);
                    }
                
            }
        } else {
            return response()->json([
                'status' => 2, 'message' => 'Voucher does not exit'
            ], Response::HTTP_OK);
        }

        $waybill = $this->waybillRepository->add_scan_vouchers($waybill, $voucher);

        return response()->json([
            'status' => 1,
            'data' => new VoucherResource($voucher),
            'message' => 'Voucher has been successfully added.'
        ], Response::HTTP_OK);
    }

    public function addExpressScanVouchers(Waybill $waybill)
    {
        return response()->json([
            'status' => 2, 'message' => 'This service temporary close'
        ], Response::HTTP_OK);
        if ($waybill->is_closed || $waybill->is_received || $waybill->is_confirm) {
            return response()->json(
                ['status' => 2, 'message' => 'Cannot add new voucher because waybill is already closed and receiced'],
                Response::HTTP_OK
            );
        }
        $voucher = $this->getVoucher(request()->get('invoice_no'));
        // dd($voucher->receiver_city_id != auth()->user()->city_id);
        if ($voucher) {
            if ($this->assignSheet($voucher) != null) {
                return response()->json([
                    'status' => 2, 'message' => 'Voucher is already assigned.'
                ], Response::HTTP_OK);
            } elseif ($voucher->postpone_date && $voucher->postpone_date->greaterThan(\Carbon\Carbon::now())) {
                return response()->json([
                    'status' => 2, 'message' => "Voucher is postponed to {$voucher->postpone_date->format('Y-m-d')} Date"
                ], Response::HTTP_OK);
            } elseif ($voucher->receiver_city_id == $voucher->sender_city_id) {
                return response()->json([
                    'status' => 2, 'message' => 'This voucher not for waybill'
                ], Response::HTTP_OK);
            // }elseif($voucher->receiver_city_id != $waybill->to_city_id){
            //     return response()->json([
            //         'status' => 2, 'message' => 'Please Choose the same receiver city with this waybill!'
            //     ], Response::HTTP_OK);
            } elseif ($voucher->delivery_status_id == 8 ) {
                return response()->json([
                    'status' => 2, 'message' => 'Voucher is already delivered.'
                ], Response::HTTP_OK);
            }elseif ($voucher->delivery_status_id == 9) {
                if($voucher->origin_city_id != auth()->user()->city_id){
                    return response()->json([
                        'status' => 2, 'message' => 'Voucher is already returned.'
                    ], Response::HTTP_OK);
                }
            }
            if (auth()->user()->city_id == 64 && $voucher->receiver_city_id != $waybill->to_city_id) {
                return response()->json([
                            'status' => 2, 'message' => 'Please Choose the same receiver city with this waybill!'
                        ], Response::HTTP_OK);
            }
        } else {
            return response()->json([
                'status' => 2, 'message' => 'Voucher does not exit'
            ], Response::HTTP_OK);
        }

        $waybill = $this->waybillRepository->add_scan_vouchers($waybill, $voucher);

        return response()->json([
            'status' => 1,
            'data' => new VoucherResource($voucher),
            'message' => 'Voucher has been successfully added.'
        ], Response::HTTP_OK);
    }

    public function receivedWaybill(Waybill $waybill)
    {
        if (!$waybill->is_closed) {
            return response()->json([
                'status' => 2, 'message' => 'Waybill need to close.'
            ], Response::HTTP_OK);
        }
        $waybill = $this->waybillRepository->received_waybill($waybill);

        if ($waybill) {
            return response()->json([
                'status' => 1, 'message' => 'Voucher has been successfully received.'
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'status' => 2, 'message' => 'Something Wrong !'
            ], Response::HTTP_OK);
        }
    }
    public function confirm_waybill(Waybill $waybill)
    {
        if ($waybill->is_closed || $waybill->is_received || $waybill->is_confirm) {
            return response()->json(
                ['status' => 2, 'message' => 'Cannot confirm because waybill is already closed or receiced or confirm'],
                Response::HTTP_OK
            );
        }

        $waybill = $this->waybillRepository->confirm_waybill($waybill);

        return new WaybillResource($waybill);
    }

    protected function getVoucher($voucher_no)
    {
        $agent_city_id = request()->get('agent_city_id');
        
        $vouchers = Voucher::with([
                    'pickup', 'pickup.sender.staff', 'pickup.sender', 'customer', 'payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
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
        } elseif ($voucher->outgoing_status === null && $voucher->origin_city_id != auth()->user()->city_id) {
            return $voucher->waybills()->latest()->first();
        }
    }

    /**
     * Store a newly uploaded image in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Waybill $waybill, FileRequest $request)
    {
        $attachment = $this->attachmentRepository->create_waybill_attachmet($waybill, $request->all());
        return new AttachmentResource($attachment);
    }
}
