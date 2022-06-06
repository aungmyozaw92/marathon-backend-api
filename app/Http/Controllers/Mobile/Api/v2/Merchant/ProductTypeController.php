<?php

namespace App\Http\Controllers\Mobile\Api\v2\Merchant;

use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\MerchantDashboard\ProductTypeRepository;
use App\Http\Resources\Mobile\v2\Merchant\ProductType\ProductTypeResource;
use App\Http\Resources\MerchantDashboard\ProductType\ProductTypeCollection;
use App\Http\Requests\MerchantDashboard\ProductType\CreateProductTypeRequest;
use App\Http\Requests\MerchantDashboard\ProductType\UpdateProductTypeRequest;

class ProductTypeController extends Controller
{
    /**
     * @var ProductTypeRepository
     */
    protected $productTypeRepository;

    /**
     * ProductController constructor.
     *
     * @param ProductTypeRepository $ProductTypeRepository
     */
    public function __construct(ProductTypeRepository $productTypeRepository)
    {
        $this->middleware('can:view,product_type')->only('show');
        $this->middleware('can:update,product_type')->only('update');
        $this->middleware('can:delete,product_type')->only('destroy');
        $this->productTypeRepository = $productTypeRepository;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProductTypeRequest $request)
    {
        $product_type = $this->productTypeRepository->create($request->only('name'));
        return new ProductTypeResource($product_type);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProductType  $product_type
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductTypeRequest $request, ProductType $product_type)
    {
        $product_type = $this->productTypeRepository->update($product_type, $request->only('name'));
        return new ProductTypeResource($product_type);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProductType  $product_type
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductType $product_type)
    {
        $this->productTypeRepository->destroy($product_type);
        return response()->json(['status' => 1, 'message' => 'Successfully Deleted!'], Response::HTTP_OK);
    }
}
