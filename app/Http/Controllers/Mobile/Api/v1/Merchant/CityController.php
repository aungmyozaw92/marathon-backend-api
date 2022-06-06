<?php

namespace App\Http\Controllers\Mobile\Api\v1\Merchant;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\City\CityResource;
use App\Http\Resources\Mobile\City\CityCollection;
use App\Repositories\Mobile\Api\v1\CityRepository;

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
        $cities = City::with('zones')->get();

        return new CityCollection($cities);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\City  $city
     * @return \Illuminate\Http\Response
     */
    public function show(City $city)
    {
        return new CityResource($city->load(['zones']));
    }
}
