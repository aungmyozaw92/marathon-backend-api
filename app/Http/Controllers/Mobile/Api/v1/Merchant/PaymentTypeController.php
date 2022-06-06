<?php

namespace App\Http\Controllers\Mobile\Api\v1\Merchant;

use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Mobile\Api\v1\PaymentTypeRepository;
use App\Http\Resources\Mobile\PaymentType\PaymentTypeResource;
use App\Http\Resources\Mobile\PaymentType\PaymentTypeCollection;


class PaymentTypeController extends Controller
{
    /**
     * @var PaymentTypeRepository
     */
    protected $paymentTypeRepository;

    /**
     * PaymentTypeController constructor.
     *
     * @param PaymentTypeRepository $paymentTypeRepository
     */
    public function __construct(PaymentTypeRepository $paymentTypeRepository)
    {
        $this->paymentTypeRepository = $paymentTypeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paymentTypes =$this->paymentTypeRepository->all();

        return new PaymentTypeCollection($paymentTypes);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PaymentType  $paymentType
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentType $paymentType)
    {
        return new PaymentTypeResource($paymentType);
    }
}
