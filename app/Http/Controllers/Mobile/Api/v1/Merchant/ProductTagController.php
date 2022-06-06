<?php

namespace App\Http\Controllers\Mobile\Api\v1\Merchant;

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = ProductTag::whereHas('tag', function($q){
                            $q->where('merchant_id', auth()->user()->id);
                        })->with(['tag','product'])->filter(request()->only([
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
