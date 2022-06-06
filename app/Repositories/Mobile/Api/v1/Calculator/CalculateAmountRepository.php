<?php

namespace App\Repositories\Mobile\Api\v1\Calculator;

use App\Models\City;
use App\Models\Gate;
use App\Models\Route;
use App\Models\Zone;
use App\Models\BusStation;
use App\Models\GlobalScale;
use App\Repositories\BaseRepository;
use App\Models\DoorToDoor;
use App\Models\BusDropOff;

class CalculateAmountRepository extends BaseRepository
{
    public function model()
    {
        return City::class;
    }
    public function calculate_delivery_amount(array $data)
    {
        $from_bus_station_id = isset($data['from_bus_station_id']) ? $data['from_bus_station_id'] : null;
        $from_gate_id = isset($data['from_gate_id']) ? $data['from_gate_id'] : null;

        $global_scale_id = isset($data['global_scale_id']) ? $data['global_scale_id'] : 1;
        $cbm = GlobalScale::findOrFail($global_scale_id);

        $route = Route::where('origin_id', $data['from_city_id'])->where('destination_id', $data['to_city_id'])->firstOrFail();
        // Get route rate for CBM Formula

        if ($from_gate_id) {
            $bus_station = BusStation::findOrFail($from_bus_station_id);
            $bus_station_rate = ($bus_station) ? $bus_station->delivery_rate : 0;
            $bus_base_data = BusDropOff::where('gate_id', $from_gate_id)->where('global_scale_id', $cbm->id)->firstOrFail();
            $bus_base_rate = ($bus_base_data) ? $bus_base_data->base_rate : 0;
        }

        $zone_rate = 0;
        $base_data = DoorToDoor::where('route_id', $route->id)->where('global_scale_id', $cbm->id)->first();
        $base_rate = ($base_data) ? $base_data->base_rate : 0;

        if (isset($data['to_zone_id']) && $data['to_zone_id'] && $base_data) {
            $zone_rate = Zone::findOrFail($data['to_zone_id'])->zone_rate;
        }

        $weight = ($data['weight'] > $cbm->support_weight) ? ($data['weight'] - $cbm->support_weight) * $base_data->salt : 0;

        if ($from_gate_id) {
            $delivery['bus_drop_off_rate'] = $bus_base_rate + $weight + $bus_station_rate;
        }
        $delivery['door_to_door_rate'] = $base_rate + $weight + $zone_rate;

        return $delivery;
    }
}
