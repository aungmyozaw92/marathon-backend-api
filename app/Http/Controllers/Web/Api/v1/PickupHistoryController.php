<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Pickup;
use Illuminate\Http\Request;
use App\Models\PickupHistory;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\PickupHistory\PickupHistoryCollection;
class PickupHistoryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Pickup $pickup)
    {

        return response()->json(['data' => ['pickup_histories' => new PickupHistoryCollection($pickup->pickup_histories)], 'status' => 1], Response::HTTP_OK);
    }
}
