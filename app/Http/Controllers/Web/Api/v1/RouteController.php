<?php

namespace App\Http\Controllers\Web\Api\v1;

use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\Route\RouteResource;
use App\Http\Resources\Route\RouteCollection;
use App\Http\Requests\Route\CreateRouteRequest;
use App\Http\Requests\Route\UpdateRouteRequest;
use App\Repositories\Web\Api\v1\RouteRepository;

class RouteController extends Controller
{
    /**
     * @var RouteRepository
     */
    protected $routeRepository;

    /**
     * RouteController constructor.
     *
     * @param RouteRepository $routeRepository
     */
    public function __construct(RouteRepository $routeRepository)
    {
        $this->routeRepository = $routeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $routes = Route::with(['origin_city', 'destination_city'])->get();
        // $routes = Route::with(['origin_city', 'destination_city'])->paginate(5);

        return new RouteCollection($routes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRouteRequest $request)
    {
        $route =$this->routeRepository->create($request->all());

        return new RouteResource($route->load(['origin_city','destination_city']));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Route  $route
     * @return \Illuminate\Http\Response
     */
    public function show(Route $route)
    {
        return new RouteResource($route->load(['origin_city','destination_city']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Route  $route
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRouteRequest $request, Route $route)
    {
        $route =$this->routeRepository->update($route, $request->all());

        return new RouteResource($route->load(['origin_city','destination_city']));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Route  $route
     * @return \Illuminate\Http\Response
     */
    public function update_price(Request $request, Route $route)
    {
        $route =$this->routeRepository->update_price($route, $request->all());

        return new RouteResource($route->load(['origin_city','destination_city']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Route  $route
     * @return \Illuminate\Http\Response
     */
    public function destroy(Route $route)
    {
        $this->routeRepository->destroy($route);

        return response()->json([ 'status' => 1 ], Response::HTTP_OK);
    }
}
