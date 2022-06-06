<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\Voucher;
use App\Models\Merchant;
use App\Models\ReturnSheet;
use Illuminate\Http\Response;
use App\Models\ReturnSheetVoucher;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MerchantReturnSheetData;
use App\Http\Resources\Voucher\VoucherCollection;
use App\Http\Requests\ReturnSheet\AddVoucherRequest;
use App\Http\Requests\DeliSheet\RemoveVoucherRequest;
use App\Repositories\Web\Api\v1\ReturnSheetRepository;
use App\Http\Resources\ReturnSheet\ReturnSheetResource;
use App\Http\Resources\ReturnSheet\ReturnSheetCollection;
use App\Http\Requests\ReturnSheet\CreateReturnSheetRequest;

class ReturnSheetController extends Controller
{
    protected $returnsheetRepository;

    /**
     * ReturnSheetController constructor.
     *
     * @param ReturnSheetRepository $returnsheetRepository
     */
    public function __construct(ReturnSheetRepository $returnsheetRepository)
    {
        $this->returnsheetRepository = $returnsheetRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->has('export')) {
            $filename = 'merchant_return_sheets.xlsx';
            Excel::store(new MerchantReturnSheetData, $filename, 'public', null, [
                    'visibility' => 'public',
            ]);
            $file = storage_path('/app/public/merchant_return_sheets.xlsx');
            return response()->download($file)->deleteFileAfterSend();
        }

        $returnSheets =  ReturnSheet::filter(request()->all())
                            ->with(['merchant', 'merchant.merchant_associates' => function ($query) {
                                $query->withTrashed();
                            }, 'vouchers', 'vouchers.pickup', 'vouchers.pickup.sender', 'vouchers.customer', 'vouchers.receiver_city', 'vouchers.receiver_zone', 'vouchers.call_status',
                            'vouchers.delivery_status', 'vouchers.store_status', 'vouchers.receiver_bus_station', 'vouchers.receiver_gate', 'vouchers.sender_city', 'vouchers.sender_zone',
                            'vouchers.sender_bus_station', 'vouchers.sender_gate', 'vouchers.payment_type', 'vouchers.parcels'])
                            ->where('merchant_id', auth()->user()->id)
                            ->orderBy('id', 'desc')
                            ->paginate(25);
        // ->get();

        return new ReturnSheetCollection($returnSheets);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ReturnSheet  $returnSheet
     * @return \Illuminate\Http\Response
     */
    public function show(ReturnSheet $returnSheet)
    {
        if ($returnSheet->merchant_id == auth()->user()->id) {
            return new ReturnSheetResource($returnSheet->load([
                'merchant', 'merchant.merchant_associates' => function ($query) {
                    $query->withTrashed();
                }, 'vouchers', 'vouchers.pickup', 'vouchers.pickup.sender', 'vouchers.customer', 'vouchers.receiver_city', 'vouchers.receiver_zone', 'vouchers.call_status',
                'vouchers.delivery_status', 'vouchers.store_status', 'vouchers.receiver_bus_station', 'vouchers.receiver_gate', 'vouchers.sender_city', 'vouchers.sender_zone',
                'vouchers.sender_bus_station', 'vouchers.sender_gate', 'vouchers.payment_type', 'vouchers.parcels'
                // 'vouchers.pickup',
                // 'vouchers.pickup.sender', 'vouchers.customer', 'vouchers.receiver_city', 'vouchers.receiver_zone',
                // 'vouchers.call_status', 'vouchers.delivery_status', 'vouchers.store_status',
                // 'vouchers.receiver_bus_station', 'vouchers.receiver_gate', 'vouchers.sender_city',
                // 'vouchers.sender_zone', 'vouchers.sender_bus_station', 'vouchers.sender_gate', 'vouchers.payment_type'
            ]));
        }

        return response()->json([
            'status' => 2, 'message' => "Unauthenticated"
        ], Response::HTTP_OK);
    }
}
