<?php

namespace App\Http\Controllers\Mobile\Api\v1\Merchant;

use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\Zone\ZoneResource;
use App\Http\Resources\Mobile\Zone\ZoneCollection;
use App\Repositories\Mobile\Api\v1\ZoneRepository;

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

        return new ZoneCollection($zones->load(['city']));
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
}
