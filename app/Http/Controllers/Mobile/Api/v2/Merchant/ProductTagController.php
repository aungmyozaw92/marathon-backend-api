<?php

namespace App\Http\Controllers\Mobile\Api\v2\Merchant;

use App\Models\ProductTag;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Mobile\Api\v1\ProductTagRepository;
use App\Http\Resources\Mobile\ProductTag\ProductTagResource;
use App\Http\Resources\Mobile\ProductTag\ProductTagCollection;
use App\Http\Requests\Mobile\ProductTag\CreateProductTagRequest;
use App\Http\Requests\Mobile\ProductTag\UpdateProductTagRequest;

class ProductTagController extends Controller
{
    /**
     * @var ProductTagRepository
     */
    protected $productTagRepository;

    /**
     * TagController constructor.
     *
     * @param ProductTagRepository $productTagRepository
     */
    public function __construct(ProductTagRepository $productTagRepository)
    {
        $this->middleware('can:view,product_tag')->only('show');
        $this->middleware('can:update,product_tag')->only('update');
        $this->middleware('can:delete,product_tag')->only('destroy');
        $this->productTagRepository = $productTagRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProductTagRequest $request)
    {
        $this->productTagRepository->create($request->all());
        return response()->json(['status' => 1, 'message' => 'Successfully Created!'], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Agent  $tag
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductTagRequest $request, ProductTag $product_tag)
    {
        $this->productTagRepository->update($product_tag, $request->all());
        return response()->json(['status' => 1, 'message' => 'Successfully Updated!'], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProductTag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductTag $product_tag)
    {
        $this->productTagRepository->destroy($product_tag);
        return response()->json(['status' => 1, 'message' => 'Successfully Deleted!'], Response::HTTP_OK);
    }
}
