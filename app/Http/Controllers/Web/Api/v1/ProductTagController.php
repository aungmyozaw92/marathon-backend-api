<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\ProductTag;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductTag\ProductTagResource;
use App\Http\Resources\ProductTag\ProductTagCollection;
use App\Http\Requests\ProductTag\CreateProductTagRequest;
use App\Http\Requests\ProductTag\UpdateProductTagRequest;
use App\Repositories\Web\Api\v1\ProductTagRepository;

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
        $this->productTagRepository = $productTagRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = ProductTag::with(['tag','product'])->filter(request()->only([
            'tag_id', 'product_id'
        ]));
        if (request()->has('paginate')) {
            $tags = $tags->paginate(request()->get('paginate'));
        } else {
            $tags = $tags->get();
        }

        return new ProductTagCollection($tags);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProductTagRequest $request)
    {
        $tag = $this->productTagRepository->create($request->all());

        return new ProductTagResource($tag->load(['tag','product']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Agent  $tag
     * @return \Illuminate\Http\Response
     */
    public function show(ProductTag $product_tag)
    {
        return new ProductTagResource($product_tag->load(['tag','product']));
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
        $product_tag = $this->productTagRepository->update($product_tag, $request->all());

        return new ProductTagResource($product_tag->load(['tag','product']));
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
        return response()->json(['status' => 1], Response::HTTP_OK);
    }

    
}
