<?php

namespace App\Http\Controllers\Web\Api\v1\ThirdParty;

use App\Models\Attachment;
use Illuminate\Http\Response;
use App\Models\ProductDiscount;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\Thirdparty\AttachmentRepository;
use App\Repositories\Web\Api\v1\ThirdParty\ProductDiscountRepository;
use App\Http\Resources\ThirdParty\ProductDiscount\ProductDiscountResource;
use App\Http\Resources\ThirdParty\ProductDiscount\ProductDiscountCollection;
use App\Http\Requests\ThirdParty\ProductDiscount\CreateProductDiscountRequest;
use App\Http\Requests\ThirdParty\ProductDiscount\UpdateProductDiscountRequest;
use App\Http\Requests\MerchantDashboard\ProductDiscount\ProductDiscountReviewRequest;
use App\Http\Requests\MerchantDashboard\ProductDiscount\UploadProductDiscountRequest;
use App\Http\Resources\ThirdParty\ProductDiscountReview\ProductDiscountReviewResource;

class ProductDiscountController extends Controller
{
    /**
     * @var ProductDiscountRepository
     */
    protected $product_discountRepository;

    /**
     * ProductController constructor.
     *
     * @param ProductDiscountRepository $Product_discountRepository
     */
    public function __construct(ProductDiscountRepository $product_discountRepository)
    {
        $this->middleware('can:view,product_discount')->only('show');
        $this->middleware('can:update,product_discount')->only('update');
        $this->middleware('can:delete,product_discount')->only('destroy');
        $this->productRepository = $product_discountRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $product_discounts = ProductDiscount::where('merchant_id', auth()->user()->id)
                            ->filter(request()->only([
                                'filter'
                            ]))
                         ->orderBy('id', 'desc');
        if (request()->has(['paginate'])) {
            $product_discounts = $product_discounts->paginate(request()->get('paginate'));
        }else{
            $product_discounts = $product_discounts->get();
        }
        return new ProductDiscountCollection($product_discounts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProductDiscountRequest $request)
    {
        $product_discount =$this->productRepository->create($request->all());

        return new ProductDiscountResource($product_discount);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ProductDiscount $product_discount)
    {
        return new ProductDiscountResource($product_discount);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProductDiscount  $product_discount
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductDiscountRequest $request, ProductDiscount $product_discount)
    {
        $product_discount =$this->productRepository->update($product_discount, $request->all());

        return new ProductDiscountResource($product_discount);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProductDiscount  $product_discount
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductDiscount $product_discount)
    {
        $this->productRepository->destroy($product_discount);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }

}
