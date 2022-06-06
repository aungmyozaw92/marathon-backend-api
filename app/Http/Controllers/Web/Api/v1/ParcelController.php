<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Parcel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Parcel\ParcelResource;
use App\Http\Resources\Parcel\ParcelCollection;
use App\Http\Requests\Parcel\CreateParcelRequest;
use App\Http\Requests\Parcel\UpdateParcelRequest;
use App\Repositories\Web\Api\v1\ParcelRepository;

class ParcelController extends Controller
{
    /**
     * @var ParcelRepository
     */
    protected $parcelRepository;

    /**
     * ParcelController constructor.
     *
     * @param ParcelRepository $parcelRepository
     */
    public function __construct(ParcelRepository $parcelRepository)
    {
        $this->parcelRepository = $parcelRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parcels = $this->parcelRepository->all();

        return new ParcelCollection($parcels);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateParcelRequest $request)
    {
        $parcel = $this->parcelRepository->create($request->all());

        return new ParcelResource($parcel);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Parcel  $parcel
     * @return \Illuminate\Http\Response
     */
    public function show(Parcel $parcel)
    {
        return new ParcelResource($parcel);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Parcel  $parcel
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateParcelRequest $request, Parcel $parcel)
    {
        $parcel = $this->parcelRepository->update($parcel, $request->all());

        return new ParcelResource($parcel);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Parcel  $parcel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Parcel $parcel)
    {
        $this->parcelRepository->destroy($parcel);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
