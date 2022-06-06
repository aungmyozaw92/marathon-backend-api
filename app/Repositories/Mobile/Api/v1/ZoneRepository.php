<?php

namespace App\Repositories\Mobile\Api\v1;

use App\Models\Zone;
use App\Repositories\BaseRepository;

class ZoneRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Zone::class;
    }

    /**
     * @param array $data
     *
     * @return Zone
     */
    public function create(array $data) : Zone
    {
        return Zone::create([
            'name' => getConvertedString($data['name']),
            'zone_rate' => $data['zone_rate'],
            'zone_agent_rate' => $data['zone_agent_rate'],
            'city_id' => $data['city_id'],
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param Zone  $zone
     * @param array $data
     *
     * @return mixed
     */
    public function update(Zone $zone, array $data) : Zone
    {
        $zone->name = getConvertedString($data['name']);
        $zone->zone_rate = $data['zone_rate'];
        $zone->zone_agent_rate = $data['zone_agent_rate'];
        $zone->city_id = $data['city_id'];

        if ($zone->isDirty()) {
            $zone->updated_by = auth()->user()->id;
            $zone->save();
        }

        return $zone->refresh();
    }

    /**
     * @param Zone $zone
     */
    public function destroy(Zone $zone)
    {
        $deleted = $this->deleteById($zone->id);

        if ($deleted) {
            $zone->deleted_by = auth()->user()->id;
            $zone->save();
        }
    }
}
