<?php

namespace App\Http\Controllers\Mobile\Api\v2\Merchant;

use App\Models\VariationMeta;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Mobile\Api\v1\VariationMetaRepository;
use App\Http\Resources\Mobile\VariationMeta\VariationMetaResource;
use App\Http\Resources\Mobile\VariationMeta\VariationMetaCollection;
use App\Http\Requests\Mobile\VariationMeta\CreateVariationMetaRequest;
use App\Http\Requests\Mobile\VariationMeta\UpdateVariationMetaRequest;

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
        if ($variation_meta) {
            return response()->json(['status' => 2, 'message' => 'Variation key and value already exit'], Response::HTTP_OK);
        }
        $this->variation_metaRepository->create($request->all());
        return response()->json(['status' => 1, 'message' => 'Successfully Created!'], Response::HTTP_OK);
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

        $this->variation_metaRepository->update($variation_meta, $request->all());
        return response()->json(['status' => 1, 'message' => 'Successfully Updated!'], Response::HTTP_OK);
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
        return response()->json(['status' => 1, 'message' => 'Successfully Deleted!'], Response::HTTP_OK);
    }
}
