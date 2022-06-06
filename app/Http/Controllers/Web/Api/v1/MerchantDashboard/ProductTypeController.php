<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\MerchantDashboard\ProductTypeRepository;
use App\Http\Resources\MerchantDashboard\ProductType\ProductTypeResource;
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $product_types = ProductType::with('products','products.attachments')->where('merchant_id', auth()->user()->id)
                        ->orderBy('id', 'desc');
        if (request()->has(['paginate'])) {
            $product_types = $product_types->paginate(request()->get('paginate'));
        }else{
            $product_types = $product_types->get();
        }
        return new ProductTypeCollection($product_types);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProductTypeRequest $request)
    {
        $product_type =$this->productTypeRepository->create($request->all());

        return new ProductTypeResource($product_type);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ProductType $product_type)
    {
        return new ProductTypeResource($product_type->load(['products','products.attachments']));
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
        $product_type =$this->productTypeRepository->update($product_type, $request->all());

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

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
