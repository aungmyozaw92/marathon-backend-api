<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Bus;
use App\Repositories\BaseRepository;

class BusRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Bus::class;
    }

    /**
     * @param array $data
     *
     * @return Bus
     */
    public function create(array $data) : Bus
    {
        return Bus::create([
            'name' => $data['name'],
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param Bus  $bus
     * @param array $data
     *
     * @return mixed
     */
    public function update(Bus $bus, array $data) : Bus
    {
        $bus->name = $data['name'];

        if($bus->isDirty()) {
            $bus->updated_by = auth()->user()->id;
            $bus->save();
        }

        return $bus->refresh();
    }


    /**
     * @param Bus $bus
     */
    public function destroy(Bus $bus)
    {
        $deleted = $this->deleteById($bus->id);

        if ($deleted) {
            $bus->deleted_by = auth()->user()->id;
            $bus->save();
        }
    }
}

