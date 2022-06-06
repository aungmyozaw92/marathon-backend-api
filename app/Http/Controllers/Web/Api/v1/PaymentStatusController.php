<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentStatus\PaymentStatusResource;
use App\Repositories\Web\Api\v1\PaymentStatusRepository;
use App\Http\Resources\PaymentStatus\PaymentStatusCollection;
use App\Http\Requests\PaymentStatus\CreatePaymentStatusRequest;
use App\Http\Requests\PaymentStatus\UpdatePaymentStatusRequest;

class PaymentStatusController extends Controller
{
    /**
     * @var PaymentStatusRepository
     */
    protected $paymentStatusRepository;

    /**
     * PaymentStatusController constructor.
     *
     * @param PaymentStatusRepository $paymentStatusRepository
     */
    public function __construct(PaymentStatusRepository $paymentStatusRepository)
    {
        $this->paymentStatusRepository = $paymentStatusRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $paymentStatuses = $this->paymentStatusRepository->all();
        $paymentStatuses = PaymentStatus::all();

        return new PaymentStatusCollection($paymentStatuses);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreatePaymentStatusRequest $request)
    {
        $paymentStatus = $this->paymentStatusRepository->create($request->all());

        return new PaymentStatusResource($paymentStatus);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PaymentStatus  $paymentStatus
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentStatus $paymentStatus)
    {
        return new PaymentStatusResource($paymentStatus);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PaymentStatus  $paymentStatus
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePaymentStatusRequest $request, PaymentStatus $paymentStatus)
    {
        $paymentStatus = $this->paymentStatusRepository->update($paymentStatus, $request->all());

        return new PaymentStatusResource($paymentStatus);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PaymentStatus  $paymentStatus
     * @return \Illuminate\Http\Response
     */
    // public function destroy(PaymentStatus $paymentStatus)
    // {
    //     $this->paymentStatusRepository->destroy($paymentStatus);
       
    //     return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    // }
}
