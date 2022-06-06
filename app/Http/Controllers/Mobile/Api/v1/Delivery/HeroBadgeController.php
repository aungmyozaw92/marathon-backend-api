<?php

namespace App\Http\Controllers\Mobile\Api\v1\Delivery;

use App\Models\HeroBadge;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\HeroBadge\HeroBadgeResource;
use App\Repositories\Web\Api\v1\HeroBadgeRepository;
use App\Http\Resources\HeroBadge\HeroBadgeCollection;
use App\Http\Requests\HeroBadge\CreateHeroBadgeRequest;
use App\Http\Requests\HeroBadge\UpdateHeroBadgeRequest;

class HeroBadgeController extends Controller
{
    /**
     * @var HeroBadgeRepository
     */
    protected $heroBadgeRepository;

    /**
     * BadgeController constructor.
     *
     * @param HeroBadgeRepository $heroBadgeRepository
     */
    public function __construct(HeroBadgeRepository $heroBadgeRepository)
    {
        $this->heroBadgeRepository = $heroBadgeRepository;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $heroBadges = HeroBadge::all();

        return new HeroBadgeCollection($heroBadges);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateHeroBadgeRequest $request)
    {
        $heroBadge = $this->heroBadgeRepository->create($request->all());

        return new HeroBadgeResource($heroBadge);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\HeroBadge  $heroBadgeBadge
     * @return \Illuminate\Http\Response
     */
    public function show(HeroBadge $heroBadge)
    {
        return new HeroBadgeResource($heroBadge);
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\HeroBadge  $heroBadge
    * @return \Illuminate\Http\Response
    */
    public function update(UpdateHeroBadgeRequest $request, HeroBadge $heroBadge)
    {
        $heroBadge = $this->heroBadgeRepository->update($heroBadge, $request->all());

        return new HeroBadgeResource($heroBadge);
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\HeroBadge  $heroBadge
    * @return \Illuminate\Http\Response
    */
    public function destroy(HeroBadge $heroBadge)
    {
        $this->heroBadgeRepository->destroy($heroBadge);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
