<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Badge\BadgeResource;
use App\Http\Resources\Badge\BadgeCollection;
use App\Http\Requests\Badge\CreateBadgeRequest;
use App\Http\Requests\Badge\UpdateBadgeRequest;
use App\Repositories\Web\Api\v1\BadgeRepository;

class BadgeController extends Controller
{
    /**
     * @var BadgeRepository
     */
    protected $badgeRepository;

    /**
     * BadgeController constructor.
     *
     * @param BadgeRepository $badgeRepository
     */
    public function __construct(BadgeRepository $badgeRepository)
    {
       $this->badgeRepository = $badgeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $badges =$this->badgeRepository->all();

        return new BadgeCollection($badges);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBadgeRequest $request)
    {
        $badge =$this->badgeRepository->create($request->all());

        return new BadgeResource($badge);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Badge  $badge
     * @return \Illuminate\Http\Response
     */
    public function show(Badge $badge)
    {
        return new BadgeResource($badge);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Badge  $badge
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBadgeRequest $request, Badge $badge)
    {
        $badge =$this->badgeRepository->update($badge, $request->all());

        return new BadgeResource($badge);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Badge  $badge
     * @return \Illuminate\Http\Response
     */
    public function destroy(Badge $badge)
    {
       $this->badgeRepository->destroy($badge);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
