<?php

namespace App\Http\Controllers\Mobile\Api\v2\Merchant;

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateInventoryRequest $request)
    {
        $inventory  = $this->inventoryRepository->create($request->all());
        if ($inventory) {
            return response()->json(['status' => 1, 'message' => 'Successfully Created!'], Response::HTTP_OK);
        } else {
            return response()->json(['status' => 5, 'message' => 'Something Went Wrong!'], Response::HTTP_OK);
        }
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
        if ($inventory) {
            return response()->json(['status' => 1, 'message' => 'Successfully Updated!'], Response::HTTP_OK);
        } else {
            return response()->json(['status' => 5, 'message' => 'Something Went Wrong!'], Response::HTTP_OK);
        }
    }

    public function add_qty(UpdateQtyRequest $request, Inventory $inventory)
    {
        $this->inventoryRepository->add_qty($inventory, $request->only('qty'));
        return response()->json(['status' => 1, 'message' => 'Successfully Added!'], Response::HTTP_OK);
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
        return response()->json(['status' => 1, 'message' => 'Successfully Deleted!'], Response::HTTP_OK);
    }
}
