<?php

namespace App\Http\Controllers\Mobile\Api\v1\Merchant;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Store\StoreResource;
use App\Repositories\Mobile\Api\v1\StoreRepository;
use App\Http\Resources\Mobile\Store\StoreCollection;
use App\Http\Requests\Mobile\Store\CreateStoreRequest;
use App\Http\Requests\Mobile\Store\UpdateStoreRequest;

class StoreController extends Controller
{
    protected $storeRepository;

    public function __construct(StoreRepository $storeRepository)
    {
        $this->middleware('can:view,store')->only('show');
        $this->middleware('can:update,store')->only('update');
        $this->middleware('can:delete,store')->only('destroy');
        $this->storeRepository = $storeRepository;
    }

    public function index()
    {
        if (request()->has(['paginate'])) {
            $stores = Store::orderBy('id', 'desc')
                    ->filter(request()->only(['search']))
                    ->paginate(20);
        }else{
            $stores = Store::orderBy('id', 'desc')
                    ->filter(request()->only(['search']))
                    ->get();
        }
        return new StoreCollection($stores);
    }

    public function store(CreateStoreRequest $request)
    {
        $store = $this->storeRepository->create($request->all());

        return new StoreResource($store);
    }

    public function show(Store $store)
    {
        return new StoreResource($store);
    }

    public function update(UpdateStoreRequest $request, Store $store)
    {
        $store = $this->storeRepository->update($store, $request->all());

        return new StoreResource($store);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function destroy(Store $store)
    {
        $this->storeRepository->destroy($store);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
