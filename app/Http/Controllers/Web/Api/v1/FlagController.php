<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Flag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Flag\FlagResource;
use App\Http\Resources\Flag\FlagCollection;
use App\Http\Requests\Flag\CreateFlagRequest;
use App\Http\Requests\Flag\UpdateFlagRequest;
use App\Repositories\Web\Api\v1\FlagRepository;

class FlagController extends Controller
{
    /**
     * @var FlagRepository
     */
    protected $flagRepository;

    /**
     * FlagController constructor.
     *
     * @param FlagRepository $flagRepository
     */
    public function __construct(FlagRepository $flagRepository)
    {
       $this->flagRepository = $flagRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $flags =$this->flagRepository->all();

        return new FlagCollection($flags);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateFlagRequest $request)
    {
        $flag =$this->flagRepository->create($request->all());

        return new FlagResource($flag);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Flag  $flag
     * @return \Illuminate\Http\Response
     */
    public function show(Flag $flag)
    {
        return new FlagResource($flag);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Flag  $flag
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFlagRequest $request, Flag $flag)
    {
        $flag =$this->flagRepository->update($flag, $request->all());

        return new FlagResource($flag);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Flag  $flag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Flag $flag)
    {
       $this->flagRepository->destroy($flag);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
