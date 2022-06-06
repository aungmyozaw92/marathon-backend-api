<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\DiscountType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\DiscountType\DiscountTypeResource;
use App\Http\Resources\DiscountType\DiscountTypeCollection;
use App\Http\Requests\DiscountType\CreateDiscountTypeRequest;
use App\Http\Requests\DiscountType\UpdateDiscountTypeRequest;
use App\Repositories\Web\Api\v1\DiscountTypeRepository;

class DiscountTypeController extends Controller
{
    /**
     * @var DiscountTypeRepository
     */
    protected $discountTypeRepository;

    /**
     * DiscountTypeController constructor.
     *
     * @param DiscountTypeRepository $discountTypeRepository
     */
    public function __construct(DiscountTypeRepository $discountTypeRepository)
    {
        $this->discountTypeRepository = $discountTypeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $discount_types = $this->discountTypeRepository->all();

        return new DiscountTypeCollection($discount_types);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CreateDiscountTypeRequest $request)
    {
        $discount_type = $this->discountTypeRepository->create($request->all());

        return new DiscountTypeResource($discount_type);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\DiscountType $discount_type
     *
     * @return \Illuminate\Http\Response
     */
    public function show(DiscountType $discount_type)
    {
        return new DiscountTypeResource($discount_type);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\DiscountType        $discount_type
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDiscountTypeRequest $request, DiscountType $discount_type)
    {
        $discount_type = $this->discountTypeRepository->update($discount_type, $request->all());

        return new DiscountTypeResource($discount_type);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\DiscountType $discount_type
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(DiscountType $discount_type)
    {
        $this->discountTypeRepository->destroy($discount_type);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
