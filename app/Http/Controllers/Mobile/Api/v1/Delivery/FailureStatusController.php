<?php

namespace App\Http\Controllers\Mobile\Api\v1\Delivery;

use App\Http\Controllers\Controller;
use App\Models\FailureStatus;
use App\Http\Resources\FailureStatus\FailureStatusResource;
use App\Http\Resources\FailureStatus\FailureStatusCollection;
class FailureStatusController extends Controller
{
    public function index()
    {
        $failureStatuses = FailureStatus::all();

        return new FailureStatusCollection($failureStatuses);
    }
}
