<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\DoorToDoor;
use App\Models\GlobalScale;
use App\Repositories\BaseRepository;

class DoorToDoorRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return DoorToDoor::class;
    }

    /**
     * @param array $data
     *
     * @return DoorToDoor
     */
    public function create(array $data): DoorToDoor
    {
        return DoorToDoor::create([
            'route_id' => $data['route_id'],
            'global_scale_id' => $data['global_scale_id'],
            'base_rate' => $data['base_rate'],
            'agent_base_rate' => $data['agent_base_rate'],
            'salt' => $data['salt'],
            'agent_salt' => $data['agent_salt'],
            'created_by' => auth()->user()->id
        ]);
    }
    /**
     * @param array $data
     *
     * @return DoorToDoor
     */
    public function create_all(array $data)
    {
        $global_scales =  GlobalScale::orderBy('id', 'ASC')->get();

       foreach ($global_scales as $key => $row) {
           
           if ($key === 0) {
             $base_rate = $data['base_rate'];
           }else{
             $base_rate = $data['base_rate'] + $data['salt'] * $key;
           }

            DoorToDoor::create([
                'route_id' => $data['route_id'],
                'global_scale_id' => $row->id,
                'base_rate' => $base_rate,
                'agent_base_rate' => $data['agent_base_rate'],
                'salt' => isset($data['salt']) ? $data['salt'] : 0,
                'agent_salt' => isset($data['agent_salt']) ? $data['agent_salt'] : 0,
                'created_by' => auth()->user()->id
            ]);
       }
       return true;
    }



    /**
     * @param DoorToDoor  $d_to_d
     * @param array $data
     *
     * @return mixed
     */
    public function update(DoorToDoor $d_to_d, array $data): DoorToDoor
    {
        $d_to_d->route_id = isset($data['route_id'])? $data['route_id'] : $d_to_d->route_id;
        $d_to_d->global_scale_id = isset($data['global_scale_id'])? $data['global_scale_id'] : $d_to_d->global_scale_id;
        $d_to_d->base_rate = isset($data['base_rate'])? $data['base_rate'] : $d_to_d->base_rate;
        $d_to_d->agent_base_rate = isset($data['agent_base_rate'])? $data['agent_base_rate'] : $d_to_d->agent_base_rate;
        $d_to_d->salt = isset($data['salt'])? $data['salt'] : $d_to_d->salt;
        $d_to_d->agent_salt = isset($data['agent_salt'])? $data['agent_salt'] : $d_to_d->agent_salt;

        if ($d_to_d->isDirty()) {
            $d_to_d->updated_by = auth()->user()->id;
            $d_to_d->save();
        }

        return $d_to_d->refresh();
    }


    /**
     * @param DoorToDoor $d_to_d
     */
    public function destroy(DoorToDoor $d_to_d)
    {
        $deleted = $this->deleteById($d_to_d->id);

        if ($deleted) {
            $d_to_d->deleted_by = auth()->user()->id;
            $d_to_d->save();
        }
    }
}
