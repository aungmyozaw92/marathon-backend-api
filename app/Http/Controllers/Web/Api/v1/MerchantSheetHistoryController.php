<?php

namespace App\Http\Controllers\Web\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MerchantSheet;
use Illuminate\Http\Response;

class MerchantSheetHistoryController extends Controller
{
    public function index(MerchantSheet $merchantSheet)
    {
        // return $merchantSheet;
        return response()->json(['data' => ['merchantsheet_histories' => $merchantSheet->merchantsheet_histories, 'voucher_histories' => []], 'status' => 1], Response::HTTP_OK);
    }
}
