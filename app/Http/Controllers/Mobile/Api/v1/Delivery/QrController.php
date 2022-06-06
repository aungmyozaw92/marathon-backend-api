<?php

namespace App\Http\Controllers\Mobile\Api\v1\Delivery;

use App\Models\QrAssociate;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\Delivery\Pickup\QrScanRequest;
use App\Http\Resources\Mobile\Delivery\Voucher\VoucherResource;

class QrController extends Controller
{
    public function scan_qr(QrScanRequest $request)
    {
        $qr_code = $request->get('qr_code');
        $qr_associate = QrAssociate::where('qr_code', $qr_code)->firstOrFail();

        $voucher = $qr_associate->voucher;
        
        if (!$qr_associate->valid || $voucher == null) {
            return response()->json(['status' => 2, "message" => "Not binding with voucher"]);
        }

        return new VoucherResource($voucher->load([
            'pickup.sender', 'customer','payment_type', 'sender_city', 'receiver_city', 'sender_zone', 'receiver_zone',
            'sender_bus_station', 'receiver_bus_station', 'sender_gate', 'receiver_gate','call_status', 'delivery_status',
            'store_status','parcels', 'pickup','pickup.sender','pickup.opened_by_staff', 'attachments' => function ($query) {
                $query->withTrashed();
            }
        ]));

    }
}
