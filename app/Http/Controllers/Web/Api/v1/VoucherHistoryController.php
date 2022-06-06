<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\VoucherHistory;
use App\Http\Controllers\Controller;
use App\Http\Resources\VoucherHistory\VoucherHistoryCollection;

class VoucherHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Voucher $voucher)
    {
        return response()->json(['data' => new VoucherHistoryCollection($voucher->voucher_histories), 'status' => 1], Response::HTTP_OK);
    }
}
