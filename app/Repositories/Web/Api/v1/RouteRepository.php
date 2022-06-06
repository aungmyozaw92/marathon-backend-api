<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\City;
use App\Models\Route;
use App\Repositories\BaseRepository;

class RouteRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Route::class;
    }

    /**
     * @param array $data
     *
     * @return Route
     */
    public function create(array $data) : Route
    {
        return Route::create([
            'origin_id' => $data['origin_id'],
            'destination_id' => $data['destination_id'],
            'route_name' => City::find($data['origin_id'])->name . '=>' . City::find($data['destination_id'])->name,
            'travel_day' => isset($data['travel_day']) ? $data['travel_day'] : 1,
           // 'route_rate' => isset($data['route_rate']) ? $data['route_rate'] : 0,
           // 'route_agent_rate' => isset($data['route_agent_rate']) ? $data['route_agent_rate'] : 0,
            'created_by' => auth()->user()->id
            // 'locked_by' => $data['locked_by'],
        ]);
    }

    /**
     * @param Route  $route
     * @param array $data
     *
     * @return mixed
     */
    public function update(Route $route, array $data) : Route
    {
        $route->origin_id = $data['origin_id'];
        $route->destination_id = $data['destination_id'];
        $route->route_name = City::find($data['origin_id'])->name . '=>' . City::find($data['destination_id'])->name;
        $route->travel_day = isset($data['travel_day']) ? $data['travel_day'] : $route->travel_day;
        //$route->route_rate = isset($data['route_rate']) ? $data['route_rate'] : $route->route_rate;
        // $route->route_agent_rate = isset($data['route_agent_rate'])?$data['route_agent_rate']:$route->route_agent_rate;

        if ($route->isDirty()) {
            $route->updated_by = auth()->user()->id;
            $route->save();
        }

        return $route->refresh();
    }
    public function update_price(Route $route, array $data) : Route
    {
       $d2d =  $route->door_to_doors()->orderBy('global_scale_id', 'ASC')->get();

       foreach ($d2d as $key => $d) {
           
           if ($key === 0) {
             $d->base_rate = $data['base_rate'];
           }else{
             $d->base_rate = $data['base_rate'] + $data['amount']*$key;
           }
           echo $data['base_rate'] + $data['amount'] * $key;
           echo '<br>';
           $d->salt = $data['amount'];
           $d->save();

       }

        return $route->refresh();
    }



    /**
     * @param Route $route
     */
    public function destroy(Route $route)
    {
        $deleted = $this->deleteById($route->id);

        if ($deleted) {
            $route->deleted_by = auth()->user()->id;
            $route->save();
        }
    }
}
