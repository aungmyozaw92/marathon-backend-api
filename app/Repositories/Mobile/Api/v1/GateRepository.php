<?php

namespace App\Repositories\Mobile\Api\v1;

use App\Models\Gate;
use App\Repositories\BaseRepository;
use App\Models\BusStation;

class GateRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Gate::class;
    }

    public function getGateByCity(array $data)
    {
        $from_station = BusStation::findOrFail($data['from_city_id']);
        $from_gate = $from_station->gates;

        $to_station = BusStation::findOrFail($data['to_city_id']);
        $to_gate = $to_station->gates;
       
        return $from_gate->merge($to_gate);
    }
}
