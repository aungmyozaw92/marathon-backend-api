<?php

namespace App\Http\Controllers\Mobile\Api\v1\Delivery;

use App\Models\Pickup;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Delivery\Pickup\PickupResource;
use App\Repositories\Mobile\Api\v1\Delivery\PickupRepository;
use App\Http\Resources\Mobile\Delivery\Pickup\PickupCollection;
use App\Http\Requests\Mobile\Delivery\Pickup\PickupUploadRequest;
use App\Http\Requests\Mobile\Delivery\Pickup\UpdatePickupRequest;
use App\Repositories\Mobile\Api\v1\Delivery\AttachmentRepository;
use App\Http\Resources\Mobile\Delivery\Attachment\AttachmentResource;
use App\Services\FirebaseService;

class PickupController extends Controller
{
    /**
     * @var PickupRepository
     */
    protected $pickupRepository;
    protected $firebaseService;
    protected $attachmentRepository;
    /**
     * PickupController constructor.
     *
     * @param PickupRepository $pickupRepository
     */
    public function __construct(PickupRepository $pickupRepository,
    AttachmentRepository $attachmentRepository,FirebaseService $firebaseService)
    {
        $this->pickupRepository = $pickupRepository;
        $this->attachmentRepository = $attachmentRepository;
        $this->firebaseService = $firebaseService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pickups = Pickup::with('sender')
                        ->where('pickuped_by_type', 'Staff')
                        ->where('pickuped_by_id', auth()->user()->id)
                        ->where('is_pickuped', 0)
                        ->where('is_closed', 0)
                        // ->whereDate('requested_date', date('Y-m-d'))
                        ->filter(request()->only([
                            'year', 'month', 'day', 'sender_name', 'sender_phone', 'sender_address',
                            'opened_by', 'note', 'search', 'is_pickuped'
                            ]))
                        ->orderBy('id', 'desc')->get();

        return new PickupCollection($pickups);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pickup  $pickup
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePickupRequest $request, Pickup $pickup)
    {
        if ($pickup->is_pickuped) {
            return response()->json(['status' => 2, "message" => "Already pickuped"]);
        }

        $pickup = $this->pickupRepository->update($pickup, $request->all());

        return new PickupResource($pickup->load([ 'opened_by_staff', 'created_by',  'sender', 'sender_associate',
         'sender_associate.phones','vouchers', 'vouchers.customer', 'vouchers.delivery_status']));
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
            // 'opened_by_staff', 
            // 'created_by',  
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
    public function getPickupHistory()
    {
        $pickups = Pickup::with('opened_by_staff', 'sender', 'sender_associate', 'sender_associate.phones')
            ->where('pickuped_by_id', auth()->user()->id)
            ->where('is_pickuped', 1)
            ->orderBy('id', 'desc')->paginate(20);

        return new PickupCollection($pickups);
    }

    /**
     * Store a newly uploaded image in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload(Pickup $pickup, PickupUploadRequest $request)
    {
        $attachment = $this->attachmentRepository->create_pickup_attachmet($pickup, $request->all());
        if($attachment) {
            $this->firebaseService->sendInternalMessage([
                'receiver_department' => 'Operation',
                'invoice' => $pickup->pickup_invoice,
                'body' => 'ဘောက်ချာစာရင်း ပို့လိုက်ပါပြီ' 
            ]);
        }
        return new AttachmentResource($attachment);
        
    }
}
