<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\ParcelItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\ParcelItem\ParcelItemResource;
use App\Http\Resources\ParcelItem\ParcelItemCollection;
use App\Http\Requests\ParcelItem\UpdateParcelItemRequest;
use App\Repositories\Web\Api\v1\ParcelItemRepository;

class ParcelItemController extends Controller
{
    /**
     * @var ParcelItemRepository
     */
    protected $parcelItemRepository;

    /**
     * parcel_itemController constructor.
     *
     * @param ParcelItemRepository $parcelItemRepository
     */
    public function __construct(ParcelItemRepository $parcelItemRepository)
    {
        $this->parcelItemRepository = $parcelItemRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parcel_items = $this->parcelItemRepository->all();

        return new ParcelItemCollection($parcel_items);
    }

    public function update(UpdateParcelItemRequest $request, ParcelItem $parcel_item)
    {
        $parcel_item = $this->parcelItemRepository->update($parcel_item, $request->all());

        return new ParcelItemResource($parcel_item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ParcelItem  $parcel_item
     * @return \Illuminate\Http\Response
     */
    public function destroy(ParcelItem $parcel_item)
    {
        $this->parcelItemRepository->destroy($parcel_item);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
