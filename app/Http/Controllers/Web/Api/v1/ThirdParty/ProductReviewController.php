<?php

namespace App\Http\Controllers\Web\Api\v1\ThirdParty;

use App\Models\ProductReview;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\Thirdparty\AttachmentRepository;
use App\Repositories\Web\Api\v1\MerchantDashboard\ProductRepository;

use App\Http\Requests\MerchantDashboard\Product\ProductReviewRequest;
use App\Http\Resources\ThirdParty\ProductReview\ProductReviewResource;
use App\Http\Requests\MerchantDashboard\Product\UpdateProductReviewRequest;

class ProductReviewController extends Controller
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * ProductController constructor.
     *
     * @param ProductRepository $ProductRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->middleware('can:view,product_review')->only('show');
        $this->middleware('can:update,product_review')->only('update');
        $this->middleware('can:view,product_review')->only('destroy');
        $this->productRepository = $productRepository;
    }
   
    public function store(ProductReviewRequest $request)
    {
        $product_review = ProductReview::where('customer_id',$request->get('customer_id'))
                                 ->where('product_id',$request->get('product_id'))
                                 ->first();
        if($product_review){
            return response()->json(['status' => 2, 'message' => 'Review already exit for this customer'], Response::HTTP_OK);
        }
        $product_review = $this->productRepository->create_review($request->all());

        return new ProductReviewResource($product_review->load(['attachemnts','customer','product']));
    }

    public function show(ProductReview $product_review)
    {
        return new ProductReviewResource($product_review->load(['attachemnts','customer','product']));
    }

     /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductReviewRequest $request, ProductReview $product_review)
    {
        $product_review =$this->productRepository->update_product_review($product_review, $request->all());

        return new ProductReviewResource($product_review->load(['attachemnts','customer','product']));
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProductReview  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductReview $product_review)
    {
        
        $this->productRepository->destroy_product_review($product_review);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
    
}
