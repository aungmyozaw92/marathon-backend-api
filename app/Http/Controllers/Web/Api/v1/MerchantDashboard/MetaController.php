<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\Meta;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Meta\MetaResource;
use App\Http\Resources\Meta\MetaCollection;
use App\Http\Requests\Meta\CreateMetaRequest;
use App\Http\Requests\Meta\UpdateMetaRequest;

class MetaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $metas = Meta::all();

        return new MetaCollection($metas);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateMetaRequest $request)
    {
        $meta = $request->storeMeta();

        return new MetaResource($meta);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Meta  $meta
     * @return \Illuminate\Http\Response
     */
    public function show(Meta $meta)
    {
        return new MetaResource($meta);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Meta  $meta
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMetaRequest $request, Meta $meta)
    {
        $meta = $request->updateMeta($meta);

        return new MetaResource($meta);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Meta  $meta
     * @return \Illuminate\Http\Response
     */
    public function destroy(Meta $meta)
    {
        $deleted = $meta->delete();

        if ($deleted) {
            $meta->deleted_by = auth()->user()->id;
            $meta->save();
        }

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
