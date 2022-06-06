<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Voucher;
use App\Models\Merchant;
use Illuminate\Support\Arr;
//use App\Http\Resources\Pickup\PickupCollection;
use Illuminate\Http\Request;
use App\Models\MerchantSheet;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\MerchantSheetVoucher;
use App\Http\Resources\Voucher\VoucherCollection;
use App\Http\Requests\DeliSheet\RemoveVoucherRequest;
use App\Http\Requests\MerchantSheet\AddVoucherRequest;
use App\Repositories\Web\Api\v1\MerchantSheetRepository;
use App\Http\Resources\MerchantSheet\MerchantSheetResource;
use App\Http\Resources\MerchantSheet\MerchantSheetCollection;
use App\Http\Requests\MerchantSheet\CreateMerchantSheetRequest;
use App\Http\Resources\MerchantSheetVoucher\MerchantSheetVoucherResource;

class MerchantSheetController extends Controller
{
    protected $merchantsheetRepository;

    /**
     * MerchantSheetController constructor.
     *
     * @param MerchantSheetRepository $merchantsheetRepository
     */
    public function __construct(MerchantSheetRepository $merchantsheetRepository)
    {
        $this->merchantsheetRepository = $merchantsheetRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $merchantSheets =  MerchantSheet::filter(request()->all())
                            ->whereHas('vouchers', function ($query) {
                                $query->where('sender_city_id', auth()->user()->city_id);
                            })
                            ->get();

        return new MerchantSheetCollection($merchantSheets->load([
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
    public function store(CreateMerchantSheetRequest $request)
    {
        // $existing_voucher_count = MerchantSheetVoucher::join('vouchers', 'voucher_id', '=', 'vouchers.id')->whereIn('voucher_id', $request->get('voucher_id'))->where('vouchers.outgoing_status', '!=', null)->count();

        // // foreach ($request->get('voucher_id') as $voucher) {
        // //     $voucher_exists = MerchantSheetVoucher::whereVoucherId($voucher)->exists();
        // //     $voucher = Voucher::findOrFail($voucher);
        // //     if ($voucher_exists && !is_null($voucher->outgoing_status)) {
        // //         return response()->json([
        // //             'status' => 4, 'message' => 'Voucher is already assigned to Merchant Sheet.'
        // //         ], Response::HTTP_OK);
        // //     }
        // // }

        // if ($existing_voucher_count > 0) {
        //     return response()->json([
        //         'status' => 4, 'message' => 'Voucher is already assigned to Merchant Sheet.'
        //     ], Response::HTTP_OK);
        //     // if ($request->get('vouchers')) {
        //     //     $request->merge([
        //     //         'vouchers' => array_unique($request->get('vouchers'), SORT_REGULAR)
        //     //     ]);

        //     //     foreach ($request->get('voucher_id') as $voucher) {
        //     //         $voucher_exists = MerchantSheetVoucher::whereVoucherId($voucher)->exists();
        //     //         $voucher = Voucher::findOrFail($voucher);
        //     //         if ($voucher_exists && !is_null($voucher->outgoing_status)) {
        //     //             return response()->json([
        //     //                 'status' => 4, 'message' => 'Voucher is already assigned to Merchant Sheet.'
        //     //             ], Response::HTTP_OK);
        //     //         }
        //     //     }
        //     // }
        // }

        // $merchantSheet = $this->merchantsheetRepository->create($request->all());

        // return new MerchantSheetResource($merchantSheet->load([
        //     'merchant', 'merchant.merchant_associates' => function ($query) {
        //         $query->withTrashed();
        //     }
        // ]));
        if ($request->get('vouchers')) {
            $request->merge([
                'vouchers' => array_unique($request->get('vouchers'), SORT_REGULAR)
            ]);

            foreach ($request->get('voucher_id') as $voucher) {
                $voucher_exists = MerchantSheetVoucher::whereVoucherId($voucher)->exists();
                $voucher = Voucher::findOrFail($voucher);
                if ($voucher_exists && !is_null($voucher->outgoing_status)) {
                    return response()->json([
                        'status' => 4, 'message' => 'Voucher is already assigned to Merchant Sheet.'
                    ], Response::HTTP_OK);
                }
            }
        }

        $merchantSheet = $this->merchantsheetRepository->create($request->all());

        return new MerchantSheetResource($merchantSheet->load([
            'merchant', 'merchant.merchant_associates' => function ($query) {
                $query->withTrashed();
            }
        ]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MerchantSheet  $merchantSheet
     * @return \Illuminate\Http\Response
     */
    public function show(MerchantSheet $merchantSheet)
    {
        return new MerchantSheetResource($merchantSheet->load([
            'merchant', 'vouchers', 'vouchers.payment_type',
            'vouchers.pickup', 'vouchers.pickup.sender', 'vouchers.customer', 'vouchers.receiver_city',
            'vouchers.receiver_zone', 'vouchers.call_status', 'vouchers.delivery_status',
            'vouchers.store_status', 'vouchers.receiver_bus_station', 'vouchers.receiver_gate',
            'vouchers.sender_city', 'vouchers.sender_zone', 'vouchers.sender_bus_station',
            'vouchers.sender_gate'
        ]));
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\MerchantSheet  $merchantSheet
     * @return \Illuminate\Http\Response
     */
    // public function filterVoucher()
    // {
    //     $vouchers = $this->merchantsheetRepository->filterVoucher(request()->all());

    //     return new VoucherCollection($vouchers->load([
    //          'customer' => function ($query) {
    //              $query->withTrashed();
    //          }
    //     ]));
    // }

    public function voucherDetails(Voucher $id)
    {
        return new MerchantSheetVoucherResource($id->load([
            'pickup', 'pickup.sender', 'customer', 'receiver_city', 'receiver_zone', 'call_status',
            'delivery_status', 'store_status', 'receiver_bus_station', 'receiver_gate', 'sender_city', 'sender_zone',
            'sender_bus_station', 'sender_gate', 'payment_type'
        ]));
    }

    public function removeVouchers(RemoveVoucherRequest $request, MerchantSheet $merchantSheet)
    {
        if (!$merchantSheet->is_closed) {
            $merchantSheet = $this->merchantsheetRepository->remove_vouchers($merchantSheet, $request->all());

            return response()->json([
                'status' => 1, 'message' => 'Voucher has been successfully removed.'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => 2, 'message' => 'Cannot remove because return sheet is already closed'
        ], Response::HTTP_OK);
    }

    public function addVouchers(AddVoucherRequest $request, MerchantSheet $merchantSheet)
    {
        if (!$merchantSheet->is_closed) {
            //  dd($request->get('voucher_id'));
            foreach ($request->get('vouchers') as $voucher) {
                $voucher = Voucher::findOrFail($voucher['id']);

                if (!$voucher->is_closed) {
                    return response()->json([
                        'status' => 4, 'message' => 'Cannot to add coz voucher need to close'
                    ], Response::HTTP_OK);
                }
                $voucher_exists = MerchantSheetVoucher::whereVoucherId($voucher['id'])->exists();

                if ($voucher_exists && !is_null($voucher->outgoing_status)) {
                    return response()->json([
                        'status' => 4, 'message' => 'Voucher is already assigned to Merchant Sheet.'
                    ], Response::HTTP_OK);
                }
            }

            $merchantSheet = $this->merchantsheetRepository->add_vouchers($merchantSheet, $request->all());

            return response()->json([
                'status' => 1, 'message' => 'Voucher has been successfully added.'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => 2, 'message' => 'Cannot add new voucher because merchant sheet is already closed'
        ], Response::HTTP_OK);
    }

    public function update(MerchantSheet $merchantSheet, Request $request)
    {
        if (!$merchantSheet->is_paid) {
            $merchantSheet = $this->merchantsheetRepository->update($merchantSheet, $request->all());

            return new MerchantSheetResource($merchantSheet->load([
                'merchant', 'vouchers', 'vouchers.payment_type',
                'vouchers.pickup', 'vouchers.pickup.sender', 'vouchers.customer', 'vouchers.receiver_city',
                'vouchers.receiver_zone', 'vouchers.call_status', 'vouchers.delivery_status',
                'vouchers.store_status', 'vouchers.receiver_bus_station', 'vouchers.receiver_gate',
                'vouchers.sender_city', 'vouchers.sender_zone', 'vouchers.sender_bus_station',
                'vouchers.sender_gate'
            ]));
        }

        return response()->json([
            'status' => 2, 'message' => 'Cannot Update because MerchantSheet is already paid.'
        ], Response::HTTP_OK);
    }
}
