<?php

namespace App\Http\Controllers\Mobile\Api\v1\Delivery;

use App\Models\Staff;
use App\Models\ReturnSheet;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReturnSheet\ReturnSheetResource;
use App\Http\Resources\ReturnSheet\ReturnSheetCollection;
use App\Repositories\Mobile\Api\v1\Delivery\ReturnSheetRepository;
use App\Http\Resources\Mobile\Delivery\Attachment\AttachmentResource;
use App\Http\Requests\Mobile\Delivery\ReturnSheet\UploadReturnSheetRequest;

class ReturnSheetController extends Controller
{
    /**
     * @var ReturnSheetRepository
     */
    protected $returnSheetRepository;

    /**
     * ReturnSheetController constructor.
     *
     * @param ReturnSheetRepository $returnSheetRepository
     */
    public function __construct(ReturnSheetRepository $returnSheetRepository)
    {
        $this->returnSheetRepository = $returnSheetRepository;
    }

    
    /**
     * Display the specified resource.
     *
     * @param  \App\ReturnSheet  $returnSheet
     * @return \Illuminate\Http\Response
     */
    public function show(ReturnSheet $returnSheet)
    {
        return new ReturnSheetResource($returnSheet->load([
            'merchant', 'merchant.merchant_associates', 'vouchers', 'vouchers.customer', 'vouchers.delivery_status',
            'vouchers.payment_type', 'vouchers.sender_city', 'vouchers.receiver_city', 'vouchers.sender_zone', 'vouchers.receiver_zone',
            'vouchers.sender_bus_station', 'vouchers.receiver_bus_station', 'vouchers.sender_gate', 'vouchers.receiver_gate', 'vouchers.call_status', 'vouchers.delivery_status',
            'vouchers.store_status', 'vouchers.parcels','vouchers.attachments'
        ]));
    }

    public function upload(UploadReturnSheetRequest $request)
    {
        $attachment = $this->returnSheetRepository->upload($request->all());

        return new AttachmentResource($attachment);
    }

    public function getReturnSheetHistory()
    {
        $delivery = Staff::findOrFail(auth()->user()->id);
        
        $returnSheets =  $delivery->return_sheets->where('is_returned', 1);

        return  new ReturnSheetCollection($returnSheets->load([
            'merchant', 'merchant.merchant_associates' => function ($query) {
                $query->withTrashed();
            }
        ]));
    }
}
