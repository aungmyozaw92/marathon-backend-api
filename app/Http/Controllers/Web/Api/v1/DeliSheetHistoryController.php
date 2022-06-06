<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\DeliSheet;
use Illuminate\Http\Request;
use App\Models\DeliSheetHistory;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\DeliSheetHistory\DeliSheetHistoryCollection;
class DeliSheetHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(DeliSheet $deliSheet)
    {
        return response()->json(['data' => ['delisheet_histories' => new DeliSheetHistoryCollection($deliSheet->delisheet_histories)], 'status' => 1], Response::HTTP_OK);
    }
}
