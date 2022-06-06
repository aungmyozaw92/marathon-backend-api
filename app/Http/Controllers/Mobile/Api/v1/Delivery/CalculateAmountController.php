<?php

namespace App\Http\Controllers\Mobile\Api\v1\Delivery;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\Delivery\CalculateAmount\CalculateAmountRequest;
use App\Repositories\Mobile\Api\v1\Delivery\CalculateAmountRepository;

class CalculateAmountController extends Controller
{
    /**
     * @var CalculateAmountRepository
     */
    protected $calculateAmountRepository;

    /**
     * CalculateAmountontroller constructor.
     *
     * @param CalculateAmountRepository $calculateAmountRepository
     */
    public function __construct(CalculateAmountRepository $calculateAmountRepository)
    {
        $this->calculateAmountRepository = $calculateAmountRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function calculate_price(CalculateAmountRequest $request)
    {
        $calculate_amount = $this->calculateAmountRepository->calculate_delivery_amount($request->all());

        return response()->json(['status' => 1, 'amount' => $calculate_amount]);
    }
}
