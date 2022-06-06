<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentType\PaymentTypeResource;
use App\Http\Resources\PaymentType\PaymentTypeCollection;
use App\Http\Requests\PaymentType\CreatePaymentTypeRequest;
use App\Http\Requests\PaymentType\UpdatePaymentTypeRequest;
use App\Repositories\Web\Api\v1\PaymentTypeRepository;

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreatePaymentTypeRequest $request)
    {
        $paymentType =$this->paymentTypeRepository->create($request->all());

        return new PaymentTypeResource($paymentType);
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PaymentType  $paymentType
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePaymentTypeRequest $request, PaymentType $paymentType)
    {
        $paymentType =$this->paymentTypeRepository->update($paymentType, $request->all());

        return new PaymentTypeResource($paymentType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PaymentType  $paymentType
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentType $paymentType)
    {
        $this->paymentTypeRepository->destroy($paymentType);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
