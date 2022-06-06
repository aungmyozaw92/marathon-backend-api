<?php

namespace App\Http\Controllers\Mobile\Api\v1\Delivery;

use App\Models\Staff;
use App\Models\Pickup;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Delivery\Voucher\VoucherCollection;
use App\Http\Resources\Mobile\Delivery\Waybill\WaybillCollection;
use App\Http\Resources\Mobile\Delivery\ReturnSheet\ReturnSheetCollection;
use App\Http\Resources\Mobile\Delivery\BusSheetVoucher\BusSheetVoucherCollection;
use App\Http\Resources\Mobile\Delivery\DeliSheetVoucher\DeliSheetVoucherCollection;

class DeliveryVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDeliveryVouchers()
    {
        
        $delivery = Staff::findOrFail(auth()->user()->id);
        $deli_voucherIds = $delivery->deli_sheets()->with('deli_sheet_vouchers')->get()->pluck('deli_sheet_vouchers')->collapse()
                                    ->where('delivery_status', 0)
                                    ->where('return', 0)
                                    ->where('cant_deliver', 0)
                                    ->pluck('voucher_id')->toArray();
        $bus_voucherIds = $delivery->bus_sheets()->with('vouchers')->get()->pluck('vouchers')->collapse()->pluck('id')->toArray();

        $voucherIds = array_merge($deli_voucherIds, $bus_voucherIds);
        $delivery_status_id = [1,2,3,4,5,6,7];
        $vouchers = Voucher::with('customer', 'payment_type','receiver_city', 'receiver_zone')
                            ->whereIn('id', $voucherIds)
                            ->whereIn('delivery_status_id', $delivery_status_id)
                            ->with('customer')
                            ->get()
                            ->sortBy('customer.name');
        
        $waybills =  $delivery->waybills
                                        // ->with('from_city', 'to_city',
                                        //         'from_bus_station', 'to_bus_station', 'gate')
                                        ->where('is_delivered', 0)
                                        ->where('is_closed', 0)
                                        ->where('is_confirm', 1)
                                        ->where('deleted_at', null);
        $returnSheets =  $delivery->return_sheets->where('is_returned', 0)->where('qty','>',0);

        // dd($waybills);
        return response()->json([
            'status' => 1,
            'data' => new VoucherCollection($vouchers->load([
                'customer', 'payment_type','receiver_city', 'receiver_zone'
            ])),
            'waybills' => new WaybillCollection($waybills->load([
                'from_city', 'to_city',
                'from_bus_station', 'to_bus_station', 'gate'
            ])),
            'return_sheets' => new ReturnSheetCollection($returnSheets->load([
                    'merchant', 'merchant.merchant_associates'
                    => function ($query) {
                        $query->withTrashed();
                    }
            ])),
            
        ]);
    }
    public function getCantDeliveryVouchers()
    {
        $delivery = Staff::findOrFail(auth()->user()->id);
        $status = request()->get('delivered');
        
        $delisheet_vouchers = $delivery->deli_sheets()->with('deli_sheet_vouchers')
                                    ->where('is_closed', 0)->get()
                                    ->pluck('deli_sheet_vouchers')->collapse()
                                    ->where('return', 0);
        
        if (!$status) {
            $deli_voucherIds = $delisheet_vouchers->where('delivery_status', 0)                           
                                            ->where('cant_deliver', 1)
                                            ->pluck('voucher_id')->toArray();
        }else{
            $deli_voucherIds = $delisheet_vouchers->where('delivery_status', 1)                           
                                            ->where('cant_deliver', 0)
                                            ->pluck('voucher_id')->toArray();
        }
        

        $vouchers = Voucher::
                    whereIn('id', $deli_voucherIds)
                    ->orderBy('id', 'desc')->get();
        return response()->json([
            'status' => 1,
            'data' => new VoucherCollection($vouchers->load(['customer']))
        ]);
    }

    public function getVoucherHistory(Request $request)
    {
        $page = $request->get('page') ?: 1;
        $offset = ($page - 1) * 20;
        $deli_voucherIds = [];

        $delivery = Staff::findOrFail(auth()->user()->id);

        $deli_sheet_voucher_ids = DB::select("SELECT deli_sheet_vouchers.voucher_id FROM deli_sheet_vouchers 
                    JOIN deli_sheets ON deli_sheet_vouchers.delisheet_id = deli_sheets.id 
                    WHERE deli_sheets.is_closed = TRUE AND deli_sheets.delivery_id = {$delivery->id} 
                    LIMIT 20 OFFSET {$offset}");

        foreach ($deli_sheet_voucher_ids as $key => $value) {
            array_push($deli_voucherIds, $value->voucher_id);
        }

        $bus_voucherIds = $delivery->bus_sheets()->whereIn('is_closed', [0, 1])->with('vouchers')->get()->pluck('vouchers')->collapse()->pluck('id')->toArray();

        $voucherIds = array_merge($deli_voucherIds, $bus_voucherIds);

        //$delivery_status_id = [8, 9, 10];
        $vouchers = Voucher::with(
            'pickup',
            'pickup.sender',
            'customer',
            'payment_type',
            'sender_city',
            'sender_zone',
            'receiver_city',
            'receiver_zone',
            'sender_bus_station',
            'receiver_bus_station',
            'sender_gate',
            'receiver_gate',
            'call_status',
            'delivery_status',
            'store_status'
        )
            ->whereIn('id', $voucherIds)
            ->orderBy('id', 'desc')->get();
        return new VoucherCollection($vouchers);
    }

    //this function is backup with two collection

    public function getVoucherHistoryBackup()
    {
        $delivery = Staff::findOrFail(auth()->user()->id);
        
        $delisheets = $delivery->deli_sheets()->where('is_closed', 1)->get();
        $vouchers = [];
        foreach ($delisheets as $delisheet) {
            $vouchers = $delisheet->deli_sheet_vouchers;
        }
        $bus_sheets = $delivery->bus_sheets()->where('is_closed', 1)->get();

        $bus_vouchers = [];
        foreach ($bus_sheets as $bus_sheet) {
            $bus_vouchers = $bus_sheet->bus_sheet_vouchers;
        }
        return response()->json([
            'delisheets' => new DeliSheetVoucherCollection($vouchers->load([
                'deli_sheet', 'voucher', 'voucher.pickup', 'voucher.pickup.sender', 'voucher.customer', 'voucher.payment_type',
                'voucher.sender_city', 'voucher.sender_zone', 'voucher.receiver_city', 'voucher.receiver_zone',
                'voucher.sender_bus_station', 'voucher.receiver_bus_station', 'voucher.sender_gate', 'voucher.receiver_gate',
                'voucher.call_status', 'voucher.delivery_status', 'voucher.store_status'
            ])),
            'bussheets' => new BusSheetVoucherCollection($bus_vouchers->load([
                'bus_sheet', 'voucher', 'voucher.pickup', 'voucher.pickup.sender', 'voucher.customer', 'voucher.payment_type',
                'voucher.sender_city', 'voucher.sender_zone', 'voucher.receiver_city', 'voucher.receiver_zone',
                'voucher.sender_bus_station', 'voucher.receiver_bus_station', 'voucher.sender_gate', 'voucher.receiver_gate',
                'voucher.call_status', 'voucher.delivery_status', 'voucher.store_status'
            ])),
        ]);
    }
}
