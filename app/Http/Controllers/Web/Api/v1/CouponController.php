<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\coupon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
// use App\Models\CouponAssociate;
use App\Http\Controllers\Controller;
use App\Http\Resources\Coupon\CouponResource;
use App\Http\Resources\Coupon\CouponCollection;
use App\Http\Requests\Coupon\CreateCouponRequest;
use App\Http\Requests\Coupon\UpdateCouponRequest;
use App\Repositories\Web\Api\v1\CouponRepository;

class CouponController extends Controller
{
    /**
     * @var couponRepository
     */
    protected $couponRepository;

    /**
     * couponController constructor.
     *
     * @param couponRepository $couponRepository
     */
    public function __construct(CouponRepository $couponRepository)
    {
        $this->couponRepository = $couponRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coupons = $this->couponRepository->all();

        return new CouponCollection($coupons);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCouponRequest $request)
    {
        $coupon = $this->couponRepository->create($request->all());

        return new CouponResource($coupon);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function show(Coupon $coupon)
    {
        return new CouponResource($coupon);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        $coupon = $this->couponRepository->update($coupon, $request->all());

        return new CouponResource($coupon);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\coupon  $coupon
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coupon $coupon)
    {
        $this->couponRepository->destroy($coupon);
        
        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }

    public function check_coupon_code(Request $request)
    {
        $coupon = $this->couponRepository->valid_coupon_code($request->all());
        if ($coupon) {
            return response()->json([ 'status' => 1, 'message' => 'Coupon code is valid' ], Response::HTTP_OK);
        } else {
            return response()->json([ 'status' => 2 , 'message' => 'Coupon code is invalid'], Response::HTTP_OK);
        }
    }
}
