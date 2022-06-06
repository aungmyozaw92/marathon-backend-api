<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\AgentBadge;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\AgentBadge\AgentBadgeResource;
use App\Repositories\Web\Api\v1\AgentBadgeRepository;
use App\Http\Resources\AgentBadge\AgentBadgeCollection;
use App\Http\Requests\AgentBadge\CreateAgentBadgeRequest;
use App\Http\Requests\AgentBadge\UpdateAgentBadgeRequest;

class AgentBadgeController extends Controller
{
    /**
     * @var AgentBadgeRepository
     */
    protected $agentBadgeRepository;

    /**
     * AgentBadgeController constructor.
     *
     * @param AgentBadgeRepository $agentBadgeRepository
     */
    public function __construct(AgentBadgeRepository $agentBadgeRepository)
    {
        $this->agentBadgeRepository = $agentBadgeRepository;
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        $agentBadges = AgentBadge::all();

        return new AgentBadgeCollection($agentBadges);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAgentBadgeRequest $request)
    {
        $agentBadge = $this->agentBadgeRepository->create($request->all());

        return new AgentBadgeResource($agentBadge);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AgentBadge  $agentBadge
     * @return \Illuminate\Http\Response
     */
    public function show(AgentBadge $agentBadge)
    {
        return new AgentBadgeResource($agentBadge);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AgentBadge  $agentBadge
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAgentBadgeRequest $request, AgentBadge $agentBadge)
    {
        $agentBadge = $this->agentBadgeRepository->update($agentBadge, $request->all());

        return new AgentBadgeResource($agentBadge);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AgentBadge  $agentBadge
     * @return \Illuminate\Http\Response
     */
    public function destroy(AgentBadge $agentBadge)
    {
        $this->agentBadgeRepository->destroy($agentBadge);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
