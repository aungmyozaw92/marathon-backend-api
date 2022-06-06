<?php

namespace App\Http\Controllers\Web\Api\v1\ThirdParty;

use App\Models\VariationMeta;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ThirdParty\VariationMeta\VariationMetaResource;
use App\Http\Resources\ThirdParty\VariationMeta\VariationMetaCollection;
use App\Http\Requests\ThirdParty\VariationMeta\CreateVariationMetaRequest;
use App\Http\Requests\ThirdParty\VariationMeta\UpdateVariationMetaRequest;
use App\Repositories\Web\Api\v1\ThirdParty\VariationMetaRepository;

class VariationMetaController extends Controller
{
    /**
     * @var VariationMetaRepository
     */
    protected $variation_metaRepository;

    /**
     * VariationMetaController constructor.
     *
     * @param VariationMetaRepository $variation_metaRepository
     */
    public function __construct(VariationMetaRepository $variation_metaRepository)
    {
        $this->middleware('can:view,variation_meta')->only('show');
        $this->middleware('can:update,variation_meta')->only('update');
        $this->middleware('can:delete,variation_meta')->only('destroy');
        $this->variation_metaRepository = $variation_metaRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $variation_metas = VariationMeta::where('merchant_id', auth()->user()->id)->filter(request()->only([
                    'search'
                ]));
        if (request()->has('paginate')) {
            $variation_metas = $variation_metas->paginate(request()->get('paginate'));
        } else {
            $variation_metas = $variation_metas->get();
        }

        return new VariationMetaCollection($variation_metas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateVariationMetaRequest $request)
    {
        $variation_meta = VariationMeta::where('merchant_id', auth()->user()->id)
                                        ->where('key', $request->get('key'))
                                        ->where('value', $request->get('value'))
                                        ->first();
        if($variation_meta){
             return response()->json(['status' => 2, 'message' => 'Variation key and value already exit'], Response::HTTP_OK);
        }
        $variation_meta = $this->variation_metaRepository->create($request->all());

        return new VariationMetaResource($variation_meta);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Agent  $variation_meta
     * @return \Illuminate\Http\Response
     */
    public function show(VariationMeta $variation_meta)
    {
        return new VariationMetaResource($variation_meta);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Agent  $variation_meta
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVariationMetaRequest $request, VariationMeta $variation_meta)
    {
        
        $variation_meta = $this->variation_metaRepository->update($variation_meta, $request->all());

        return new VariationMetaResource($variation_meta);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VariationMeta  $variation_meta
     * @return \Illuminate\Http\Response
     */
    public function destroy(VariationMeta $variation_meta)
    {
        $this->variation_metaRepository->destroy($variation_meta);
        return response()->json(['status' => 1], Response::HTTP_OK);
    }

    
}
