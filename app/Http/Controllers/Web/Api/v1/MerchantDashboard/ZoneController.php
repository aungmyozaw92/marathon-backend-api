<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Zone\ZoneResource;
use App\Http\Resources\Zone\ZoneCollection;
use App\Repositories\Web\Api\v1\ZoneRepository;
use App\Http\Requests\Zone\CreateZoneRequest;
use App\Http\Requests\Zone\UpdateZoneRequest;

class ZoneController extends Controller
{
    /**
     * @var ZoneRepository
     */
    protected $zoneRepository;

    /**
     * ZoneController constructor.
     *
     * @param ZoneRepository $zoneRepository
     */
    public function __construct(ZoneRepository $zoneRepository)
    {
        $this->zoneRepository = $zoneRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $zones = $this->zoneRepository->all();
        $zones = Zone::filter(request()->all())->get();

        return new ZoneCollection($zones->load(['city']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateZoneRequest $request)
    {
        $zone =$this->zoneRepository->create($request->all());

        return new ZoneResource($zone->load(['city']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Zone  $zone
     * @return \Illuminate\Http\Response
     */
    public function show(Zone $zone)
    {
        return new ZoneResource($zone->load(['city']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Zone  $zone
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateZoneRequest $request, Zone $zone)
    {
        $zone = $this->zoneRepository->update($zone, $request->all());

        return new ZoneResource($zone->load(['city']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Zone  $zone
     * @return \Illuminate\Http\Response
     */
    public function destroy(Zone $zone)
    {
        $this->zoneRepository->destroy($zone);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
