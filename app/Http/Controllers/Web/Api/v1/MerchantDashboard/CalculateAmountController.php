<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Mobile\CalculateAmount\CalculateAmountRequest;
use App\Repositories\Mobile\Api\v1\CalculateAmountRepository;

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
