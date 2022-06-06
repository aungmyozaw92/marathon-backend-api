<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Flag;
use App\Repositories\BaseRepository;

class FlagRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Flag::class;
    }

    /**
     * @param array $data
     *
     * @return Flag
     */
    public function create(array $data) : Flag
    {
        return Flag::create([
            'name' => $data['name'],
            'logo' => $data['logo'],
            'description' => $data['description'],
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param Flag  $flag
     * @param array $data
     *
     * @return mixed
     */
    public function update(Flag $flag, array $data) : Flag
    {
        $flag->name = $data['name'];
        $flag->logo = $data['logo'];
        $flag->description = $data['description'];

        if($flag->isDirty()) {
            $flag->updated_by = auth()->user()->id;
            $flag->save();
        }

        return $flag->refresh();
    }

    /**
     * @param Flag $flag
     */
    public function destroy(Flag $flag)
    {
        $deleted = $this->deleteById($flag->id);

        if ($deleted) {
            $flag->deleted_by = auth()->user()->id;
            $flag->save();
        }
    }
}

