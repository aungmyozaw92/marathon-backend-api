<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\BusDropOff;
use App\Repositories\BaseRepository;

class BusDropOffRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return BusDropOff::class;
    }

    /**
     * @param array $data
     *
     * @return BusDropOff
     */
    public function create(array $data): BusDropOff
    {
        return BusDropOff::create([
            'route_id' => $data['route_id'],
            'gate_id' => $data['gate_id'],
            'global_scale_id' => $data['global_scale_id'],
            'base_rate' => $data['base_rate'],
            'agent_base_rate' => $data['agent_base_rate'],
            'salt' => $data['salt'],
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param BusDropOff  $bus_drop_off
     * @param array $data
     *
     * @return mixed
     */
    public function update(BusDropOff $bus_drop_off, array $data): BusDropOff
    {
        $bus_drop_off->route_id = isset($data['route_id'])? $data['route_id'] : $bus_drop_off->route_id;
        $bus_drop_off->gate_id = isset($data['gate_id'])? $data['gate_id'] : $bus_drop_off->gate_id;
        $bus_drop_off->global_scale_id = isset($data['global_scale_id'])? $data['global_scale_id'] : $bus_drop_off->global_scale_id;
        $bus_drop_off->base_rate = isset($data['base_rate'])? $data['base_rate'] : $bus_drop_off->base_rate;
        $bus_drop_off->agent_base_rate = isset($data['agent_base_rate'])? $data['agent_base_rate'] : $bus_drop_off->agent_base_rate;
        $bus_drop_off->salt = isset($data['salt'])? $data['salt'] : $bus_drop_off->salt;
        // $bus_drop_off->agent_salt = isset($data['agent_salt'])? $data['agent_salt'] : $bus_drop_off->agent_salt;

        if ($bus_drop_off->isDirty()) {
            $bus_drop_off->updated_by = auth()->user()->id;
            $bus_drop_off->save();
        }

        return $bus_drop_off->refresh();
    }


    /**
     * @param BusDropOff $bus_drop_off
     */
    public function destroy(BusDropOff $bus_drop_off)
    {
        $deleted = $this->deleteById($bus_drop_off->id);

        if ($deleted) {
            $bus_drop_off->deleted_by = auth()->user()->id;
            $bus_drop_off->save();
        }
    }
}
