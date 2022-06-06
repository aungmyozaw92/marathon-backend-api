<?php

namespace App\Http\Controllers\Web\Api\v1\ThirdParty;

use App\Models\ProductVariation;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ThirdParty\ProductVariation\ProductVariationResource;
use App\Http\Resources\ThirdParty\ProductVariation\ProductVariationCollection;
use App\Http\Requests\ThirdParty\ProductVariation\CreateProductVariationRequest;
use App\Http\Requests\ThirdParty\ProductVariation\UpdateProductVariationRequest;
use App\Repositories\Web\Api\v1\ThirdParty\ProductVariationRepository;

class ProductVariationController extends Controller
{
    /**
     * @var ProductVariationRepository
     */
    protected $product_variationRepository;

    /**
     * ProductVariationController constructor.
     *
     * @param ProductVariationRepository $product_variationRepository
     */
    public function __construct(ProductVariationRepository $product_variationRepository)
    {
        $this->middleware('can:view,product_variation')->only('show');
        $this->middleware('can:update,product_variation')->only('update');
        $this->middleware('can:delete,product_variation')->only('destroy');
        $this->product_variationRepository = $product_variationRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $product_variations = ProductVariation::with('product','variation_meta')
                                                    ->whereHas('product', function($q){
                                                        $q->where('merchant_id', auth()->user()->id);
                                                    })->orWhereHas('variation_meta', function($q){
                                                        $q->where('merchant_id', auth()->user()->id);
                                                    });
        if (request()->has('paginate')) {
            $product_variations = $product_variations->paginate(request()->get('paginate'));
        } else {
            $product_variations = $product_variations->get();
        }

        return new ProductVariationCollection($product_variations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProductVariationRequest $request)
    {
        $product_variation = ProductVariation::where('product_id', $request->get('product_id'))
                                        ->where('variation_meta_id', $request->get('variation_meta_id'))
                                        ->first();
        if($product_variation){
             return response()->json(['status' => 2, 'message' => 'Variation meta and product already exit'], Response::HTTP_OK);
        }
        $product_variation = $this->product_variationRepository->create($request->all());

        return new ProductVariationResource($product_variation->load(['product','variation_meta']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Agent  $product_variation
     * @return \Illuminate\Http\Response
     */
    public function show(ProductVariation $product_variation)
    {
        return new ProductVariationResource($product_variation->load(['product','variation_meta']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Agent  $product_variation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductVariationRequest $request, ProductVariation $product_variation)
    {
        
        $product_variation = $this->product_variationRepository->update($product_variation, $request->all());

        return new ProductVariationResource($product_variation->load(['product','variation_meta']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProductVariation  $product_variation
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductVariation $product_variation)
    {
        $this->product_variationRepository->destroy($product_variation);
        return response()->json(['status' => 1], Response::HTTP_OK);
    }

    
}
