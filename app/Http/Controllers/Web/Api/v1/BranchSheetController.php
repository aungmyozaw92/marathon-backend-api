<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\BranchSheet;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\BranchSheetVoucher;
use App\Repositories\Web\Api\v1\BranchSheetRepository;
use App\Http\Resources\BranchSheet\BranchSheetResource;
use App\Http\Resources\BranchSheet\BranchSheetCollection;
use App\Http\Requests\BranchSheet\CreateBranchSheetRequest;

class BranchSheetController extends Controller
{
    protected $branchsheetRepository;

    /**
     * BranchSheetController constructor.
     *
     * @param BranchSheetRepository $branchsheetRepository
     */
    public function __construct(BranchSheetRepository $branchsheetRepository)
    {
        $this->branchsheetRepository = $branchsheetRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branchSheets =  BranchSheet::filter(request()->all())->get();

        return new BranchSheetCollection($branchSheets->load([
            'branch' => function ($query) {
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
    public function store(CreateBranchSheetRequest $request)
    {
        $existing_voucher_count = BranchSheetVoucher::join('vouchers', 'voucher_id', '=', 'vouchers.id')->whereIn('voucher_id', $request->get('voucher_id'))->where('vouchers.outgoing_status', '!=', null)->count();

        // foreach ($request->get('voucher_id') as $voucher) {
        //     $voucher_exists = BranchSheetVoucher::whereVoucherId($voucher)->exists();
        //     $voucher = Voucher::findOrFail($voucher);
        //     if ($voucher_exists && !is_null($voucher->outgoing_status)) {
        //         return response()->json([
        //             'status' => 4, 'message' => 'Voucher is already assigned to Merchant Sheet.'
        //         ], Response::HTTP_OK);
        //     }
        // }

        if ($existing_voucher_count > 0) {
            return response()->json([
                'status' => 4, 'message' => 'Voucher is already assigned to Branch Sheet.'
            ], Response::HTTP_OK);
        }

        $branchSheet = $this->branchsheetRepository->create($request->all());

        return new BranchSheetResource($branchSheet->load([
            'branch' => function ($query) {
                $query->withTrashed();
            }
        ]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BranchSheet  $branchSheet
     * @return \Illuminate\Http\Response
     */
    public function show(BranchSheet $branchSheet)
    {
        return new BranchSheetResource($branchSheet->load([
            'branch', 'vouchers', 'vouchers.payment_type',
            'vouchers.customer', 'vouchers.call_status', 'vouchers.delivery_status',
            'vouchers.store_status'
        ]));
    }


    // public function voucherDetails(Voucher $id)
    // {
    //     return new BranchSheetVoucherResource($id->load([
    //         'pickup', 'pickup.sender', 'customer', 'receiver_city', 'receiver_zone', 'call_status',
    //         'delivery_status', 'store_status', 'receiver_bus_station', 'receiver_gate', 'sender_city', 'sender_zone',
    //         'sender_bus_station', 'sender_gate', 'payment_type'
    //     ]));
    // }
}
