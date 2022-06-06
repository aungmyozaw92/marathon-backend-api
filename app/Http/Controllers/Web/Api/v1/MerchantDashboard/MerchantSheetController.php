<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\Voucher;
use App\Models\Merchant;
use Illuminate\Support\Arr;
//use App\Http\Resources\Pickup\PickupCollection;
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
use App\Repositories\Web\Api\v1\MerchantDashboard\TransactionRepository;
use App\Http\Resources\MerchantSheetVoucher\MerchantSheetVoucherResource;
use App\Http\Requests\MerchantDashboard\Transaction\CreateWithdrawRequest;

class MerchantSheetController extends Controller
{
    protected $merchantsheetRepository;

    /**
     * MerchantSheetController constructor.
     *
     * @param MerchantSheetRepository $merchantsheetRepository
     */
    public function __construct(MerchantSheetRepository $merchantsheetRepository,TransactionRepository $transactionRepository)
    {
        $this->merchantsheetRepository = $merchantsheetRepository;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $merchantSheets =  MerchantSheet::filter(request()->all())
            ->where('merchant_id', auth()->user()->id)
            ->get();

        return new MerchantSheetCollection($merchantSheets->load([
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
        if ($merchantSheet->merchant_id == auth()->user()->id) {
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
            'status' => 2, 'message' => "Unauthenticated"
        ], Response::HTTP_OK);
    }

    public function create_withdraw(CreateWithdrawRequest $request)
    {
        $response = $this->transactionRepository->create_withdraw($request->all());
        // return response()->json(['status' => 1, 'message' => 'Successfully Requested a Withdraw!'], Response::HTTP_OK);
        return  $response;
    }
}
