<?php

namespace App\Http\Controllers\Web\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ReturnSheet;
use Illuminate\Http\Response;
use App\Http\Resources\ReturnSheetHistory\ReturnSheetHistoryCollection;

class ReturnSheetHistoryController extends Controller
{
    public function index(ReturnSheet $returnSheet)
    {
        return response()->json(['data' => ['return_sheet_histories' => new ReturnSheetHistoryCollection($returnSheet->return_sheet_histories)], 'status' => 1], Response::HTTP_OK);
    }
}
