<?php

namespace App\Http\Controllers\Mobile\Api\v2\Merchant;

use App\Models\ReturnSheet;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\v2\Merchant\ReturnSheet\ReturnSheetResource;
use App\Http\Resources\Mobile\v2\Merchant\ReturnSheet\ReturnSheetCollection;

class ReturnSheetController extends Controller
{
    protected $returnsheetRepository;

    /**
     * ReturnSheetController constructor.
     *
     * @param ReturnSheetRepository $returnsheetRepository
     */
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $returnSheets =  ReturnSheet::filter(request()->all())
                            ->with(['merchant_associate' => function ($query) {
                                $query->withTrashed();
                            }])
                            ->where('merchant_id', auth()->user()->id)
                            ->filter(request()->only(['is_returned']))
                            ->orderBy('id', 'desc')
                            ->paginate(25);

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
                'merchant_associate' => function ($query) {
                    $query->withTrashed();
                }, 'vouchers', 'vouchers.receiver','attachments'
               
            ]));
        }

        return response()->json([
            'status' => 2, 'message' => "This rethrn sheet is unauthorized."
        ], Response::HTTP_OK);
    }
}
