<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Voucher;
use App\Models\Merchant;
use Illuminate\Support\Arr;
//use App\Http\Resources\Pickup\PickupCollection;
use Illuminate\Http\Request;
use App\Models\AgentSheet;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\AgentSheetVoucher;
use App\Http\Resources\Voucher\VoucherCollection;

use App\Repositories\Web\Api\v1\AgentSheetRepository;
use App\Http\Resources\AgentSheet\AgentSheetResource;
use App\Http\Resources\AgentSheet\AgentSheetCollection;
use App\Http\Requests\AgentSheet\CreateAgentSheetRequest;
use App\Http\Resources\AgentSheetVoucher\AgentSheetVoucherResource;

class AgentSheetController extends Controller
{
    protected $agentsheetRepository;

    /**
     * AgentSheetController constructor.
     *
     * @param AgentSheetRepository $agentsheetRepository
     */
    public function __construct(AgentSheetRepository $agentsheetRepository)
    {
        $this->agentsheetRepository = $agentsheetRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $agentSheets =  AgentSheet::filter(request()->all())->get();

        return new AgentSheetCollection($agentSheets->load([
            'merchant', 'merchant.merchant_associates' => function ($query) {
                $query->withTrashed();
            }
        ]));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAgentSheetRequest $request)
    {
        foreach ($request->get('voucher_id') as $voucher) {
            $voucher_exists = AgentSheetVoucher::whereVoucherId($voucher)->exists();
            $voucher = Voucher::findOrFail($voucher);
            if ($voucher_exists && !is_null ($voucher->outgoing_status)) {
                return response()->json([
                    'status' => 4, 'message' => 'Voucher is already assigned to Merchant Sheet.'
                ], Response::HTTP_OK);
            }
        }
        
        $merchantSheet = $this->agentsheetRepository->create($request->all());

        return new AgentSheetResource($merchantSheet->load(['merchant', 'merchant.merchant_associates' => function ($query) {
            $query->withTrashed();
        }
        ]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AgentSheet  $merchantSheet
     * @return \Illuminate\Http\Response
     */
    public function show(AgentSheet $merchantSheet)
    {
        return new AgentSheetResource($merchantSheet->load([ 'merchant', 'vouchers', 'vouchers.payment_type',
            // 'vouchers.pickup', 'vouchers.pickup.sender', 'vouchers.customer', 'vouchers.receiver_city',
            // 'vouchers.receiver_zone', 'vouchers.call_status', 'vouchers.delivery_status',
            // 'vouchers.store_status', 'vouchers.receiver_bus_station', 'vouchers.receiver_gate',
            // 'vouchers.sender_city', 'vouchers.sender_zone', 'vouchers.sender_bus_station',
            // 'vouchers.sender_gate', 'vouchers.payment_type'
        ]));
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\AgentSheet  $merchantSheet
     * @return \Illuminate\Http\Response
     */
    // public function filterVoucher()
    // {
    //     $vouchers = $this->agentsheetRepository->filterVoucher(request()->all());
        
    //     return new VoucherCollection($vouchers->load([
    //          'customer' => function ($query) {
    //              $query->withTrashed();
    //          }
    //     ]));
    // }

    public function voucherDetails(Voucher $id)
    {
        return new AgentSheetVoucherResource($id->load([
            'pickup', 'pickup.sender', 'customer', 'receiver_city', 'receiver_zone', 'call_status',
            'delivery_status', 'store_status', 'receiver_bus_station', 'receiver_gate', 'sender_city', 'sender_zone',
            'sender_bus_station', 'sender_gate', 'payment_type'
        ]));
    }
}
