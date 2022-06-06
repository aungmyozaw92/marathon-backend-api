<?php

namespace App\Http\Controllers\Web\Api\v1\Delivery;

use App\Models\Pickup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryWeb\Pickup\PickupResource;
use App\Http\Resources\DeliveryWeb\Pickup\PickupCollection;

class PickupController extends Controller
{
    public function index()
    {
        $orderBy = (request()->has('orderBy'))? request()->get('orderBy') : 'desc';
        $pickups = Pickup::with('sender','sender_associate')
                        ->where('pickuped_by_type', 'Staff')
                        ->where('pickuped_by_id', auth()->user()->id)
                        // ->where('is_pickuped', 0)
                        // ->where('is_closed', 0)
                        ->filter(request()->only([
                            'year', 'month', 'day', 'date', 'is_paid', 'is_closed','pickup_date',
                            'sender_name', 'sender_phone', 'sender_address',
                            'note', 'search', 'is_pickuped'
                            ]))
                        ->orderBy('id', $orderBy);

        if (request()->has('paginate')) {
            $pickups = $pickups->paginate(request()->get('paginate'));
        } else {
            $pickups = $pickups->get();
        }

        return new PickupCollection($pickups);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pickup  $pickup
     * @return \Illuminate\Http\Response
     */
    public function show(Pickup $pickup)
    {
        return new PickupResource($pickup->load([
            'assigned_by', 
            'created_by',  
            'point_logs',  
            'sender', 
            'sender_associate', 
            // 'sender_associate.phones' ,
            'vouchers', 
            'vouchers.customer', 
            // 'vouchers.delivery_status',
            'vouchers.payment_type', 
            // 'vouchers.sender_city', 
            // 'vouchers.receiver_city', 
            // 'vouchers.sender_zone', 
            // 'vouchers.receiver_zone',
            // 'vouchers.sender_bus_station', 
            // 'vouchers.receiver_bus_station', 
            // 'vouchers.sender_gate', 
            // 'vouchers.receiver_gate', 
            // 'vouchers.call_status', 
            // 'vouchers.delivery_status',
            'vouchers.store_status', 
            //'vouchers.parcels',
            // 'vouchers.attachments',
            'attachments'
            ]));
    }
}
