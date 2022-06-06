<?php

namespace App\Http\Controllers\Mobile\Api\v1\Merchant;

use App\Models\Inventory;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositories\Mobile\Api\v1\InventoryRepository;
use App\Http\Requests\Mobile\Inventory\UpdateQtyRequest;
use App\Http\Resources\Mobile\Inventory\InventoryResource;
use App\Http\Resources\Mobile\Inventory\InventoryCollection;
use App\Http\Requests\Mobile\Inventory\CreateInventoryRequest;
use App\Http\Requests\Mobile\Inventory\UpdateInventoryRequest;

class InventoryController extends Controller
{
    /**
     * @var InventoryRepository
     */
    protected $inventoryRepository;

    /**
     * InventoryController constructor.
     *
     * @param InventoryRepository $inventoryRepository
     */
    public function __construct(InventoryRepository $inventoryRepository)
    {
        $this->middleware('can:view,inventory')->only('show');
        $this->middleware('can:update,inventory')->only('update');
        $this->middleware('can:delete,inventory')->only('destroy');
        $this->inventoryRepository = $inventoryRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $inventorys = Inventory::with('product')->whereHas('product', function($q){
                                    $q->where('merchant_id', auth()->user()->id);
                                })->filter(request()->only(['product_id']));
        if (request()->has('paginate')) {
            $inventorys = $inventorys->paginate(request()->get('paginate'));
        } else {
            $inventorys = $inventorys->get();
        }
        return new InventoryCollection($inventorys);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateInventoryRequest $request)
    {
        $inventory = $this->inventoryRepository->create($request->all());

        return new InventoryResource($inventory->load('product'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show(Inventory $inventory)
    {
        return new InventoryResource($inventory->load('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInventoryRequest $request, Inventory $inventory)
    {
        $inventory = $this->inventoryRepository->update($inventory, $request->all());

        return new InventoryResource($inventory->load('product'));
    }

    public function add_qty(UpdateQtyRequest $request, Inventory $inventory)
    {
        $inventory = $this->inventoryRepository->add_qty($inventory, $request->only('qty'));

        return new InventoryResource($inventory->load('product'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventory $inventory)
    {
        $this->inventoryRepository->destroy($inventory);
        return response()->json(['status' => 1], Response::HTTP_OK);
    }

    
}
