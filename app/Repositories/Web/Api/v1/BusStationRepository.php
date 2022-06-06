<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\BusStation;
use App\Repositories\BaseRepository;

class BusStationRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return BusStation::class;
    }

    /**
     * @param array $data
     *
     * @return BusStation
     */
    public function create(array $data) : BusStation
    {
        // if (isset($data['name_mm'])) {
            $name = getConvertedString($data['name']);
        // }

        return BusStation::create([
            'name' => $name,
            // 'lat' => $data['lat'],
            // 'long' => $data['long'],
            'delivery_rate' => $data['delivery_rate'],
            'number_of_gates' => 0,
            'city_id' => $data['city_id'],
            'zone_id' => $data['zone_id'],
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param BusStation  $busStation
     * @param array $data
     *
     * @return mixed
     */
    public function update(BusStation $busStation, array $data) : BusStation
    {
        if (isset($data['name'])) {
            $name = getConvertedString($data['name']);
        }

        $count = $busStation->gates->count() + 1;
        $busStation->name = isset($data['name']) ? $name : $busStation->name;
        // $busStation->lat = isset($data['lat'])?$data['lat']:$busStation->lat;
        // $busStation->long = isset($data['long'])?$data['long']:$busStation->long;
        $busStation->number_of_gates = $count;
        $busStation->city_id = isset($data['city_id'])?$data['city_id']:$busStation->city_id;
        $busStation->zone_id = isset($data['zone_id'])?$data['zone_id']:$busStation->zone_id;
        $busStation->delivery_rate = isset($data['delivery_rate'])?$data['delivery_rate']:$busStation->delivery_rate;

        if ($busStation->isDirty()) {
            $busStation->updated_by = auth()->user()->id;
            $busStation->save();
        }

        return $busStation->refresh();
    }

    /**
     * @param BusStation $busStation
     */
    public function destroy(BusStation $busStation)
    {
        $deleted = $this->deleteById($busStation->id);

        if ($deleted) {
            $busStation->deleted_by = auth()->user()->id;
            $busStation->save();
        }
    }
}
