<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\City;
use App\Repositories\BaseRepository;

class CityRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return City::class;
    }

    /**
     * @param array $data
     *
     * @return City
     */
    public function create(array $data) : City
    {
        //if (isset($data['name'])) {
            $name = getConvertedString($data['name']);
        //}

        return City::create([
            'name' => $name,
            // 'delivery_rate' => $data['delivery_rate'],
            'is_collect_only' => $data['is_collect_only'],
            'is_on_demand' => $data['is_on_demand'],
            'is_available_d2d' => $data['is_available_d2d'],
            // 'is_active' => isset($data['is_active']) ? $data['is_active'] : 0,
            // 'locking' => isset($data['locking']) ? $data['locking'] : 0,
            'created_by' => auth()->user()->id
            // 'locked_by' => $data['locked_by'],
        ]);
    }

    /**
     * @param City  $city
     * @param array $data
     *
     * @return mixed
     */
    public function update(City $city, array $data) : City
    {
        if (isset($data['name'])) {
            $name = getConvertedString($data['name']);
        }

        $city->name = isset($data['name']) ? $name : $city->name;
        $city->is_collect_only = isset($data['is_collect_only']) ? $data['is_collect_only'] : $city->is_collect_only;
        $city->is_on_demand = isset($data['is_on_demand']) ? $data['is_on_demand'] : $city->is_on_demand;
        $city->is_available_d2d = isset($data['is_available_d2d']) ? $data['is_available_d2d'] : $city->is_available_d2d;
        // $city->delivery_rate = $data['delivery_rate'];
        // $city->is_active = isset($data['is_active']) ? $data['is_active'] : $city->is_active;
        // $city->locking = isset($data['locking']) ? $data['locking'] : $city->locking;
        // $city->locked_by = $data['locked_by'];

        if ($city->isDirty()) {
            $city->updated_by = auth()->user()->id;
            $city->save();
        }

        return $city->refresh();
    }


    /**
     * @param City $city
     */
    public function destroy(City $city)
    {
        $deleted = $this->deleteById($city->id);

        if ($deleted) {
            $city->deleted_by = auth()->user()->id;
            $city->save();
        }
    }
}
