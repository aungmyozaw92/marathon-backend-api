<?php

namespace App\Http\Controllers\Mobile\Api\v1\Delivery;

use App\Models\Zone;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Delivery\Zone\ZoneResource;
use App\Http\Resources\Mobile\Delivery\Zone\ZoneCollection;


use App\Repositories\Mobile\Api\v1\Delivery\ZoneRepository;

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
        $zones = $this->zoneRepository->all();

        return new ZoneCollection($zones);
    }

    
    /**
     * Display the specified resource.
     *
     * @param  \App\Zone  $zone
     * @return \Illuminate\Http\Response
     */
    public function show(Zone $zone)
    {
        return new ZoneResource($zone);
    }
}
