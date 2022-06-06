<?php

namespace App\Http\Controllers\Mobile\Api\v1\Delivery;

use App\Models\Staff;
use App\Models\Waybill;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Mobile\Api\v1\Delivery\WaybillRepository;
use App\Http\Resources\Mobile\Delivery\Waybill\WaybillResource;
use App\Http\Resources\Mobile\Delivery\Waybill\WaybillCollection;
use App\Http\Requests\Mobile\Delivery\Waybill\UploadWaybillRequest;
use App\Http\Resources\Mobile\Delivery\Attachment\AttachmentResource;

class WaybillController extends Controller
{
    /**
     * @var WaybillRepository
     */
    protected $waybillRepository;

    /**
     * WaybillController constructor.
     *
     * @param WaybillRepository $waybillRepository
     */
    public function __construct(WaybillRepository $waybillRepository)
    {
        $this->waybillRepository = $waybillRepository;
    }

    
    /**
     * Display the specified resource.
     *
     * @param  \App\Waybill  $waybill
     * @return \Illuminate\Http\Response
     */
    public function show(Waybill $waybill)
    {
        return new WaybillResource($waybill->load([
             'delivery', 'staff', 'from_city', 'to_city',
            'from_bus_station', 'to_bus_station', 'gate', 'vouchers', 
            //'vouchers.pickup' , 'vouchers.pickup.sender',
            'vouchers.customer', 
            //'vouchers.receiver_city',
             //'vouchers.receiver_zone',
            // 'vouchers.sender_bus_station', 
            // 'vouchers.receiver_bus_station', 
            // 'vouchers.sender_gate',
            //  'vouchers.receiver_gate', 
            // 'vouchers.call_status',
            //'vouchers.delivery_status', 
            //'vouchers.store_status', 
            'vouchers.payment_type', 
            'city','attachments'
        ]));
    }

    public function upload(UploadWaybillRequest $request)
    {
        $waybill = Waybill::find($request->get('waybill_id'));
        if(!$waybill->is_confirm){
            return response()->json([
                'status' => 1,'message' => 'Cannot deliver because this waybill need to confirm. '
            ], Response::HTTP_OK);
        }
        $attachment = $this->waybillRepository->upload($request->all());
        if(request()->has('file')){
            return new AttachmentResource($attachment);
        }else{
            return response()->json([
                'status' => 1
            ], Response::HTTP_OK);
        }
       
    }

    public function getWaybillHistory()
    {
        $delivery = Staff::findOrFail(auth()->user()->id);
        
        $waybills =  $delivery->waybills->where('is_delivered', 1);

        return  new WaybillCollection($waybills->load([
            'delivery', 'staff', 'from_city', 'to_city',
            'from_bus_station', 'to_bus_station', 'gate'
        ]));
    }
}
