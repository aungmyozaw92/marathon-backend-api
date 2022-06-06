<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Web\Api\v1\ProductTypeRepository;
use App\Http\Resources\ProductType\ProductTypeResource;
use App\Http\Resources\ProductType\ProductTypeCollection;
use App\Http\Requests\ProductType\CreateProductTypeRequest;
use App\Http\Requests\ProductType\UpdateProductTypeRequest;

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
        $this->productTypeRepository = $productTypeRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $product_types = ProductType::orderBy('id', 'desc')->get();
        return new ProductTypeCollection($product_types->load(['merchant']));
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

        return new ProductTypeResource($product_type->load(['merchant']));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ProductType $product_type)
    {
        return new ProductTypeResource($product_type->load(['merchant']));
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

        return new ProductTypeResource($product_type->load(['merchant']));
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
