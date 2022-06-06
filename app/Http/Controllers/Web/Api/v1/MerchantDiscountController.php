<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\MerchantDiscount;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\MerchantDiscount\MerchantDiscountResource;
use App\Http\Resources\MerchantDiscount\MerchantDiscountCollection;
use App\Repositories\Web\Api\v1\MerchantDiscountRepository;
use App\Http\Requests\MerchantDiscount\CreateMerchantDiscountRequest;
use App\Http\Requests\MerchantDiscount\UpdateMerchantDiscountRequest;

class MerchantDiscountController extends Controller
{
    /**
     * @var MerchantDiscountRepository
     */
    protected $merchantDiscountRepository;
    
    /**
     * MerchantDiscountController constructor.
     *
     * @param MerchantDiscountRepository $merchantDiscountRepository
     */
    public function __construct(MerchantDiscountRepository $merchantDiscountRepository)
    {
        $this->merchantDiscountRepository = $merchantDiscountRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $$merchantDiscounts = $this->merchantDiscountRepository->all();
        if (request()->has('paginate')) {
            $merchantDiscounts = MerchantDiscount::filter(request()->only(['merchant_id']))->paginate(25);
        } else {
            $merchantDiscounts = MerchantDiscount::filter(request()->only(['merchant_id']))->get();
        }

        return new MerchantDiscountCollection($merchantDiscounts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateMerchantDiscountRequest $request)
    {
        $merchantDiscount = $this->merchantDiscountRepository->create($request->all());

        return new MerchantDiscountResource($merchantDiscount);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MerchantDiscount  $merchantDiscount
     * @return \Illuminate\Http\Response
     */
    public function show(MerchantDiscount $merchantDiscount)
    {
        return new MerchantDiscountResource($merchantDiscount);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MerchantDiscount  $merchantDiscount
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMerchantDiscountRequest $request, MerchantDiscount $merchantDiscount)
    {
        $merchantDiscount = $this->merchantDiscountRepository->update($merchantDiscount, $request->all());

        return new MerchantDiscountResource($merchantDiscount);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MerchantDiscount  $merchantDiscount
     * @return \Illuminate\Http\Response
     */
    public function destroy(MerchantDiscount $merchantDiscount)
    {
        $this->merchantDiscountRepository->destroy($merchantDiscount);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
