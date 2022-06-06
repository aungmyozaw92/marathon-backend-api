<?php

namespace App\Http\Controllers\Web\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Waybill;
use Illuminate\Http\Response;
use App\Http\Resources\WaybillHistory\WaybillHistoryCollection;

class WaybillHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Waybill $waybill)
    {
        return response()->json(['data' => ['waybill_histories' => new WaybillHistoryCollection($waybill->waybill_histories)], 'status' => 1], Response::HTTP_OK);
    }
}
