<?php

namespace App\Http\Controllers\Web\Api\v1\Delivery;

use App\Models\DeliSheet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryWeb\DeliSheet\DeliSheetResource;
use App\Http\Resources\DeliveryWeb\DeliSheet\DeliSheetCollection;

class DeliSheetController extends Controller
{
    public function index()
    {
        $orderBy = (request()->has('orderBy'))? request()->get('orderBy') : 'desc';
        $delisheets = DeliSheet::deliveryFilter(
                        request()->only([
                            'date', 'is_closed', 'is_paid','start_date','end_date'
                        ]))->with('courier_type','deli_sheet_vouchers')
                        ->orderBy('id', $orderBy);

        if (request()->has('paginate')) {
            $delisheets = $delisheets->paginate(request()->get('paginate'));
        } else {
            $delisheets = $delisheets->get();
        }

        return new DeliSheetCollection($delisheets);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DeliSheet  $delisheet
     * @return \Illuminate\Http\Response
     */
    public function show(DeliSheet $deli_sheet)
    {
        return new DeliSheetResource($deli_sheet->load([
            'courier_type','deli_sheet_vouchers','vouchers'
        ]));
    }
}
