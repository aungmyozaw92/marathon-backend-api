<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Voucher;
use App\Models\Merchant;
use App\Models\ReturnSheet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\ReturnSheetVoucher;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\FileRequest;
use App\Http\Resources\Voucher\VoucherCollection;
use App\Http\Requests\ReturnSheet\AddVoucherRequest;
use App\Http\Requests\DeliSheet\RemoveVoucherRequest;
use App\Repositories\Web\Api\v1\ReturnSheetRepository;
use App\Http\Resources\ReturnSheet\ReturnSheetResource;
use App\Http\Resources\ReturnSheet\ReturnSheetCollection;
use App\Http\Requests\ReturnSheet\CreateReturnSheetRequest;
use App\Http\Requests\ReturnSheet\ChangeHeroRequest;

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
        $returnSheets =  ReturnSheet::with([
            'merchant', 'delivery', 'merchant.merchant_associates', 'issuer', 'closed_by' 
            => function ($query) {
                $query->withTrashed();
            }, 'attachments'
        ])
            ->filter(request()->all())
            ->where(function ($query) {
                if(auth()->user()->hasRole('Agent')){
                    $query->whereHas('merchant', function($q){
                        $q->whereHas('city', function($q) {
                            $q->whereDoesntHave('branch');
                        }); 
                    });
                }else{
                    $query->whereHas('vouchers', function ($qr) {
                        $qr->where('sender_city_id', auth()->user()->city_id);
                    })->orWhereDoesntHave('vouchers');
                }
                
            })
            ->orderBy('id', 'desc');

        if (request()->has('paginate')) {
            $returnSheets = $returnSheets->paginate(request()->get('paginate'));
        } else {
            $returnSheets = $returnSheets->get();
        }

        return new ReturnSheetCollection(
            $returnSheets
            // , 'vouchers', 'vouchers.pickup', 'vouchers.pickup.sender', 'vouchers.customer', 'vouchers.receiver_city', 'vouchers.receiver_zone', 'vouchers.call_status',
            // 'vouchers.delivery_status', 'vouchers.store_status', 'vouchers.receiver_bus_station', 'vouchers.receiver_gate', 'vouchers.sender_city', 'vouchers.sender_zone',
            // 'vouchers.sender_bus_station', 'vouchers.sender_gate', 'vouchers.payment_type', 'vouchers.parcels'
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateReturnSheetRequest $request)
    {
        if ($request->get('vouchers')) {
            $request->merge([
                'vouchers' => array_unique($request->get('vouchers'), SORT_REGULAR)
            ]);

            foreach ($request->get('vouchers') as $voucher) {
                $voucher_exists = ReturnSheetVoucher::whereVoucherId($voucher['id'])->exists();
                $voucher = Voucher::findOrFail($voucher['id']);
                if ($voucher_exists && !is_null($voucher->outgoing_status)) {
                    return response()->json([
                        'status' => 4, 'message' => 'Voucher is already assigned to Return Sheet.'
                    ], Response::HTTP_OK);
                }
            }
        }

        $returnSheet = $this->returnsheetRepository->create($request->all());

        return new ReturnSheetResource($returnSheet->load([
            'merchant', 'delivery', 'merchant.merchant_associates' => function ($query) {
                $query->withTrashed();
            }, 'attachments'
        ]));
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
            'merchant', 'delivery', 'merchant.merchant_associates', 'issuer' , 'closed_by' 
            => function ($query) {
                $query->withTrashed();
            }, 'vouchers', 'vouchers.pickup', 'vouchers.pickup.sender', 'vouchers.customer', 'vouchers.receiver_city', 'vouchers.receiver_zone', 'vouchers.call_status',
            'vouchers.delivery_status', 'vouchers.store_status', 'vouchers.receiver_bus_station', 'vouchers.receiver_gate', 'vouchers.sender_city', 'vouchers.sender_zone',
            'vouchers.sender_bus_station', 'vouchers.sender_gate', 'vouchers.payment_type', 'vouchers.parcels', 'attachments','vouchers.pending_returning_actor'
            // 'vouchers.pickup',
            // 'vouchers.pickup.sender', 'vouchers.customer', 'vouchers.receiver_city', 'vouchers.receiver_zone',
            // 'vouchers.call_status', 'vouchers.delivery_status', 'vouchers.store_status',
            // 'vouchers.receiver_bus_station', 'vouchers.receiver_gate', 'vouchers.sender_city',
            // 'vouchers.sender_zone', 'vouchers.sender_bus_station', 'vouchers.sender_gate', 'vouchers.payment_type'
        ]));
    }

    public function removeVouchers(RemoveVoucherRequest $request, ReturnSheet $returnSheet)
    {
        if ($returnSheet->is_came_from_mobile && $returnSheet->actby_mobile !== null) {
            return response()->json(['status' => 2, "message" => "Can't remove voucher. Hero confirmed delivering status."]);
        }
        if (!$returnSheet->is_closed) {
            $returnSheet = $this->returnsheetRepository->remove_vouchers($returnSheet, $request->all());

            return response()->json([
                'status' => 1, 'message' => 'Voucher has been successfully removed.'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => 2, 'message' => 'Cannot remove because return sheet is already closed'
        ], Response::HTTP_OK);
    }
    public function addVouchers(AddVoucherRequest $request, ReturnSheet $returnSheet)
    {
        if ($returnSheet->is_came_from_mobile && $returnSheet->actby_mobile !== null) {
            return response()->json(['status' => 2, "message" => "Can't add voucher. Hero confirmed delivering status."]);
        }
        if (!$returnSheet->is_closed) {
            if ($request->get('vouchers')) {
                foreach ($request->get('vouchers') as $voucher) {
                    $voucher_exists = ReturnSheetVoucher::whereVoucherId($voucher['id'])->exists();
                    $voucher = Voucher::findOrFail($voucher['id']);
                    if ($voucher_exists && !is_null($voucher->outgoing_status)) {
                        return response()->json([
                            'status' => 4, 'message' => 'Voucher is already assigned to Return Sheet.'
                        ], Response::HTTP_OK);
                    }
                }
            }

            $returnSheet = $this->returnsheetRepository->add_vouchers($returnSheet, $request->all());

            return response()->json([
                'status' => 1, 'message' => 'Voucher has been successfully added.'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => 2, 'message' => 'Cannot add new voucher because return sheet is already closed'
        ], Response::HTTP_OK);
    }

    public function closed(Request $request, ReturnSheet $returnSheet)
    {
        if (!$returnSheet->is_closed) {
            $returnSheet = $this->returnsheetRepository->closed($returnSheet, $request->all());

            return response()->json([
                'status' => 1, 'message' => 'Returnsheet has been successfully closed.'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => 2, 'message' => 'Cannot close because this return sheet is already closed'
        ], Response::HTTP_OK);
    }

    /**
     * Upload ReturnSheet's Image
     */
    public function upload(ReturnSheet $returnSheet, FileRequest $request)
    {
        if ($request->hasFile('file') && $file = $request->file('file')) {
            $voucher = $this->returnsheetRepository->upload($returnSheet, $file);
        }

        return new ReturnSheetResource($returnSheet->load([
            'merchant', 'delivery', 'merchant.merchant_associates' => function ($query) {
                $query->withTrashed();
            }, 'vouchers', 'vouchers.pickup', 'vouchers.pickup.sender', 'vouchers.customer', 'vouchers.receiver_city', 'vouchers.receiver_zone', 'vouchers.call_status',
            'vouchers.delivery_status', 'vouchers.store_status', 'vouchers.receiver_bus_station', 'vouchers.receiver_gate', 'vouchers.sender_city', 'vouchers.sender_zone',
            'vouchers.sender_bus_station', 'vouchers.sender_gate', 'vouchers.payment_type', 'vouchers.parcels', 'attachments'
        ]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ReturnSheet  $returnSheet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ReturnSheet $returnSheet)
    {
        // if ($returnSheet->is_closed) {
        //     return response()->json([
        //                 'status' => 2, 'message' => 'Cannot delete because this deliSheet is already closed'
        //             ], Response::HTTP_OK);
        // }

        $voucher_count = $returnSheet->vouchers->count();

        if ($voucher_count > 0) {
            return response()->json([
                'status' => 2, 'message' => 'Cannot delete because this Return Sheet has ' . $voucher_count . ' voucher'
            ], Response::HTTP_OK);
        }

        $this->returnsheetRepository->destroy($returnSheet);

        return response()->json(['status' => 1, 'message' => 'Successfully updated'], Response::HTTP_OK);
    }
    public function change_hero(ChangeHeroRequest $request)
    {
        $returnSheet = ReturnSheet::find($request->get('return_sheet_id'));
        if ($returnSheet->delivery->department_id == 5 && $returnSheet->is_came_from_mobile && $returnSheet->actby_mobile != null) {
            return response()->json(['status' => 2, "message" => "Can't change hero. Hero confirmed to return"]);
        } else {
            $returnSheet = $this->returnsheetRepository->change_hero($returnSheet, $request->all());

            return new ReturnSheetResource($returnSheet->load([
                'merchant', 'delivery', 'merchant.merchant_associates' => function ($query) {
                    $query->withTrashed();
                }, 'vouchers', 'vouchers.pickup', 'vouchers.pickup.sender', 'vouchers.customer', 'vouchers.receiver_city', 'vouchers.receiver_zone', 'vouchers.call_status',
                'vouchers.delivery_status', 'vouchers.store_status', 'vouchers.receiver_bus_station', 'vouchers.receiver_gate', 'vouchers.sender_city', 'vouchers.sender_zone',
                'vouchers.sender_bus_station', 'vouchers.sender_gate', 'vouchers.payment_type', 'vouchers.parcels', 'attachments'
            ]));
        }
    }

    public function manual_closed(ReturnSheet $returnSheet){
        if (!$returnSheet->is_closed) {
            $returnSheet->is_closed = 1;
            // $returnSheet->is_paid = 1;
            $returnSheet->is_returned = 1;
            $returnSheet->closed_date = now();
            $returnSheet->closed_by = auth()->user()->id;

            $returnSheet->save();

            return response()->json([
                'status' => 1, 'message' => 'Returnsheet has been successfully closed.'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'status' => 2, 'message' => 'Cannot close because this return sheet is already closed'
        ], Response::HTTP_OK);
    }
}
