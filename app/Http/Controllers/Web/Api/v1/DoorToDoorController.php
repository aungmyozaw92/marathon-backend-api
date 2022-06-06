<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\DoorToDoor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\DoorToDoor\CreateAllRequest;
use App\Http\Resources\DoorToDoor\DoorToDoorResource;
use App\Repositories\Web\Api\v1\DoorToDoorRepository;
use App\Http\Resources\DoorToDoor\DoorToDoorCollection;
use App\Http\Requests\DoorToDoor\CreateDoorToDoorRequest;
use App\Http\Requests\DoorToDoor\UpdateDoorToDoorRequest;

class DoorToDoorController extends Controller
{
    /**
     * @var DoorToDoorRepository
     */
    protected $doorToDoorRepository;

    /**
     * DoorToDoorController constructor.
     *
     * @param DoorToDoorRepository $doorToDoorRepository
     */
    public function __construct(DoorToDoorRepository $doorToDoorRepository)
    {
        $this->doorToDoorRepository = $doorToDoorRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        // $door_to_doors = $this->doorToDoorRepository->all();
        $door_to_doors = DoorToDoor::with([
            'route', 'global_scale', 'route.origin_city',
            'route.destination_city'
        ])
            ->filter(request()->only(['search']))
            ->order(request()->only([
                'sortBy', 'orderBy'
            ]))
            ->paginate(25);

        // return new DoorToDoorCollection($door_to_doors->load([
        //     'route', 'global_scale','route.origin_city','route.destination_city' => function ($query) {
        //         $query->withTrashed();
        //     }
        // ]));
        return new DoorTODoorCollection($door_to_doors);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateDoorToDoorRequest $request)
    {
        $door_to_door = $this->doorToDoorRepository->create($request->all());

        return new DoorToDoorResource($door_to_door);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create_all(CreateAllRequest $request)
    {
        $this->doorToDoorRepository->create_all($request->all());

        return response()->json(['status' => 1], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DoorToDoor  $door_to_door
     * @return \Illuminate\Http\Response
     */
    public function show(DoorToDoor $door_to_door)
    {
        return new DoorToDoorResource($door_to_door);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DoorToDoor  $door_to_door
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDoorToDoorRequest $request, DoorToDoor $door_to_door)
    {
        $door_to_door = $this->doorToDoorRepository->update($door_to_door, $request->all());

        return new DoorToDoorResource($door_to_door);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DoorToDoor  $door_to_door
     * @return \Illuminate\Http\Response
     */
    public function destroy(DoorToDoor $door_to_door)
    {
        $this->doorToDoorRepository->destroy($door_to_door);

        return response()->json(['status' => 1], Response::HTTP_OK);
    }
}
