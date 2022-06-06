<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\GlobalScale;
use App\Repositories\BaseRepository;

class GlobalScaleRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return GlobalScale::class;
    }

    /**
     * @param array $data
     *
     * @return GlobalScale
     */
    public function create(array $data) : GlobalScale
    {
        if (isset($data['description_mm'])) {
            $description_mm = getConvertedString($data['description_mm']);
        }

        return GlobalScale::create([
            'cbm' => $data['cbm'],
            'support_weight' => $data['support_weight'],
            'max_weight' => $data['max_weight'],
            //'global_scale_rate' => $data['global_scale_rate'],
            //'global_scale_agent_rate' => $data['global_scale_agent_rate'],
            //'salt' => $data['salt'],
            'description' => $data['description'],
            'description_mm' => ($data['description_mm']) ? $description_mm : null,
            //'bus_fee' => $data['bus_fee'],
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param GlobalScale  $global_scale
     * @param array $data
     *
     * @return mixed
     */
    public function update(GlobalScale $global_scale, array $data) : GlobalScale
    {
        $global_scale->cbm = $data['cbm'];
        $global_scale->support_weight = $data['support_weight'];
        $global_scale->max_weight = $data['max_weight'];
        // $global_scale->global_scale_rate = $data['global_scale_rate'];
        // $global_scale->global_scale_agent_rate = $data['global_scale_agent_rate'];
        // $global_scale->salt = $data['salt'];
        $global_scale->description = $data['description'];
        $global_scale->description_mm = $data['description_mm'];
        // $global_scale->bus_fee = $data['bus_fee'];

        if ($global_scale->isDirty()) {
            $global_scale->updated_by = auth()->user()->id;
            $global_scale->save();
        }
        return $global_scale->refresh();
    }

    /**
     * @param Badge $global_scale
     */
    public function destroy(GlobalScale $global_scale)
    {
        $deleted = $this->deleteById($global_scale->id);

        if ($deleted) {
            $global_scale->deleted_by = auth()->user()->id;
            $global_scale->save();
        }
    }
}
