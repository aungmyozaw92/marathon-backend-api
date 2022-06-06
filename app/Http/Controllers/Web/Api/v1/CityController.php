<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\City\CityResource;
use App\Http\Resources\City\CityCollection;
use App\Http\Requests\City\CreateCityRequest;
use App\Http\Requests\City\UpdateCityRequest;
use App\Http\Resources\Agent\AgentCollection;
use App\Repositories\Web\Api\v1\CityRepository;

class CityController extends Controller
{
    /**
     * @var CityRepository
     */
    protected $cityRepository;

    /**
     * CityController constructor.
     *
     * @param CityRepository $cityRepository
     */
    public function __construct(CityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $cities = $this->cityRepository->all();
        $cities = City::with('zones', 'agent', 'branch', 'agents')->get();

        return new CityCollection($cities);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCityRequest $request)
    {
        $city = $this->cityRepository->create($request->all());

        return new CityResource($city);
    }
    public function update_specify_data(Request $request)
    {
        $cities = $this->cityRepository->all();
        $count = 0;
        foreach ($cities as $city) {
            // echo $request->get('data')[$count];
            // echo '<br>';
            $city->name_mm = $request->get('data')[$count];
            $city->save();
            $count++;
        }

        dd('OK');
    }
 
    /**
     * Display the specified resource.
     *
     * @param  \App\City  $city
     * @return \Illuminate\Http\Response
     */
    public function show(City $city)
    {
        return new CityResource($city->load(['zones','agent','branch']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\City  $city
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCityRequest $request, City $city)
    {
        $city = $this->cityRepository->update($city, $request->all());

        return new CityResource($city);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\City  $city
     * @return \Illuminate\Http\Response
     */
    public function destroy(City $city)
    {
        $this->cityRepository->destroy($city);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAgents(City $city)
    {
        // $cities = $this->cityRepository->all();
        $agents = $city->agents;
        if (!$agents) {
          return response()->json([ 'status' => 2, 'message'=> 'There is no agent in this city!' ], Response::HTTP_OK);
        }

        return new AgentCollection($agents->load('city'));
    }
}
