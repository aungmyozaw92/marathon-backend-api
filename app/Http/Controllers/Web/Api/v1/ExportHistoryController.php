<?php

namespace App\Http\Controllers\Web\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PickupHistory;
use App\Models\DeliSheetHistory;
use App\Models\DeliSheetVoucher;
use App\Models\WaybillHistory;
use App\Models\MerchantSheetHistory;
use App\Models\Voucher;
use App\Models\VoucherHistory;
use Validator;

class ExportHistoryController extends Controller
{
    public function export_sheet(Request $request)
    {
        $referer = $request->segments(1)[2];
        $logStatusId = getStatusId($request->logStatus);
        if ($referer === "export_pickup" || $referer === "print_pickup") {
            $result = new PickupHistory([
                'pickup_id' => $request->sheet_id,
                'log_status_id' => $logStatusId,
                'created_by' => auth()->user()->id
            ]);
            auth()->user()->pickup_histories()->save($result);
        } elseif ($referer === "export_delisheet" || $referer === "print_delisheet") {
            $result = DeliSheetHistory::create([
                'delisheet_id' => $request->sheet_id,
                'log_status_id' => $logStatusId,
                'created_by' => auth()->user()->id
            ]);
        } elseif ($referer === "export_waybill" || $referer === "print_waybill") {
            $result = WaybillHistory::create([
                'waybill_id' => $request->sheet_id,
                'log_status_id' => $logStatusId,
                'created_by_type' => 'Staff',
                'created_by' => auth()->user()->id
            ]);
        } elseif ($referer === "export_merchantsheet" || $referer === "print_merchantsheet") {
            $result = MerchantSheetHistory::create([
                'merchant_sheet_id' => $request->sheet_id,
                'log_status_id' => $logStatusId,
                'created_by' => auth()->user()->id
            ]);
        } elseif ($referer === "print_voucher") {
            $result = new VoucherHistory([
                'voucher_id' => $request->sheet_id,
                'log_status_id' => $logStatusId
            ]);
            auth()->user()->voucher_histories()->save($result);
        } else {
            return response()->json([
                'status' => 2, 'message' => "log can't be triggered!"
            ], 200);
        }
        return response(['status' => 1, 'message' => 'Success'], 200);
        // switch ($referer) {
        //     case "export_pickup":
        //         $result = new PickupHistory([
        //             'pickup_id' => $request->sheet_id,
        //             'log_status_id' => $logStatusId
        //         ]);
        //         auth()->user()->pickup_histories()->save($result);
        //         break;
        //     case "export_delisheet":
        //         $result = DeliSheetHistory::create([
        //             'delisheet_id' => $request->sheet_id,
        //             'log_status_id' => $logStatusId,
        //             'created_by' => auth()->user()->id
        //         ]);
        //         break;
        //     case "export_waybill":
        //         $result = WaybillHistory::create([
        //             'waybill_id' => $request->sheet_id,
        //             'log_status_id' => $logStatusId,
        //             'created_by' => auth()->user()->id
        //         ]);
        //         break;
        //     default:
        //         return response()->json([
        //             'status' => 2, 'message' => "log can't be triggered!"
        //         ], 200);
        // }
        if ($result) {
            return response(['status' => 1, 'message' => 'Success'], 200);
        }
    }


    // public function export_pickup(Request $request)
    // {
    //     $logStatusId = getStatusId($request->logStatus);
    //     $result = new PickupHistory([
    //         'pickup_id' => $request->sheet_id,
    //         'log_status_id' => $logStatusId
    //     ]);
    //     auth()->user()->pickup_histories()->save($result);
    //     if ($result) {
    //         return response(['status' => 1, 'message' => 'Success'], 200);
    //     }
    //     return response()->json([
    //         'status' => 2, 'message' => "log can't be triggered!"
    //     ], 200);
    // }
    // public function export_delisheet(Request $request)
    // {
    //     $logStatusId = getStatusId($request->logStatus);
    //     $result = DeliSheetHistory::create([
    //         'delisheet_id' => $request->sheet_id,
    //         'log_status_id' => $logStatusId
    //     ]);
    //     if ($result) {
    //         return response(['status' => 1, 'message' => 'Success'], 200);
    //     }
    //     return response()->json([
    //         'status' => 2, 'message' => "log can't be triggered!"
    //     ], 200);
    // }
    // public function export_waybill(Request $request)
    // {
    //     $logStatusId = getStatusId($request->logStatus);
    //     DeliSheetHistory::create([
    //         'waybill_id' => $request->sheet_id,
    //         'log_status_id' => $logStatusId
    //     ]);
    //     return response(['status' => 1, 'message' => 'Success'], 200);
    // }

    public function sheet_event(Request $request)
    {
        $referer = $request->segments(1)[2];
        $logStatusId = getStatusId($request->logStatus);
        if ($request->voucherNote != null) {
            $note = str_replace(",", "|", $request->voucherNote);
            Voucher::where('id', $request->voucherInvoice)->update(['delivery_status_id' => 10]);
            DeliSheetVoucher::where('delisheet_id', $request->sheet_id)
                ->where('voucher_id', $request->voucherInvoice)
                ->update(['note' => $note]);
            return response()->json(['status' => 1, 'message' => 'Saved Reason'], 200);
        }
        DeliSheetHistory::create([
            'delisheet_id' => $request->sheet_id,
            'log_status_id' => $logStatusId,
            'previous' => $request->voucherInvoice,
            'created_by' => auth()->user()->id
        ]);
        return response()->json(['status' => 1, 'message' => 'Success'], 200);
    }
}
