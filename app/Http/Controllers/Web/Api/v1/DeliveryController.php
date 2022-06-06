<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Pickup;
use App\Models\Waybill;
use App\Models\BusSheet;
use App\Models\Delivery;
use App\Models\DeliSheet;
use App\Models\ReturnSheet;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryUnpaidSheet\DeliveryUnpaidSheetCollection;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function delivery_unpiad_sheets(Delivery $delivery)
    {
        $deli_sheets = DeliSheet::where('delivery_id', $delivery->id)->where('is_paid', 0)->get();
        $waybills = Waybill::where('delivery_id', $delivery->id)->where('is_paid', 0)->get();
        $bus_sheets = BusSheet::where('delivery_id', $delivery->id)->where('is_paid', 0)->get();
        $pickups = Pickup::where('pickuped_by_type', 'Staff')->where('pickuped_by_id', $delivery->id)->where('is_paid', 0)->get();

        $all_sheets = ($deli_sheets->merge($waybills))->merge($bus_sheets);

        return response()->json([
            'status' => 1,
            'all_sheets' => $all_sheets,
        ], Response::HTTP_OK);
    }

    public function unpiad_sheets(Delivery $delivery)
    {
        
        $deli_sheets = DeliSheet::filter(request()->only(['created_at']))->where('delivery_id', $delivery->id)->get();
   
        $waybills = Waybill::filter(request()->only(['created_at']))->with(['point_logs','created_by_staff'])->where('delivery_id', $delivery->id)->get();
        $bus_sheets = BusSheet::where('delivery_id', $delivery->id)->get();
        $pickups = Pickup::filter(request()->only(['created_at']))->with(['point_logs','created_by'])->where('pickuped_by_type', 'Staff')->where('pickuped_by_id', $delivery->id)->get();
        $return_sheets = ReturnSheet::filter(request()->only(['created_at']))->with(['point_logs','created_by_staff'])->where('delivery_id', $delivery->id)->get();

        $all_sheets = ($deli_sheets->merge($waybills))->merge($return_sheets)->merge($pickups);

        return new DeliveryUnpaidSheetCollection($all_sheets);

        return response()->json([
            'status' => 1,
            'all_sheets' => $all_sheets,
        ], Response::HTTP_OK);
    }
}
