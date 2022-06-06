<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\DelegateDuration;
use App\Repositories\BaseRepository;

class DelegateDurationRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return DelegateDuration::class;
    }

    /**
     * @param array $data
     *
     * @return DelegateDuration
     */
    public function create(array $data) : DelegateDuration
    {
        return DelegateDuration::create([
            'time' => $data['time'],
            'value' => $data['value'],
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param DelegateDuration  $delegate_duration
     * @param array $data
     *
     * @return mixed
     */
    public function update(DelegateDuration $delegate_duration, array $data) : DelegateDuration
    {
        $delegate_duration->time = $data['time'];
        $delegate_duration->value = $data['value'];

        if ($delegate_duration->isDirty()) {
            $delegate_duration->updated_by = auth()->user()->id;
            $delegate_duration->save();
        }

        return $delegate_duration->refresh();
    }

    /**
     * @param DelegateDuration $delegate_duration
     */
    public function destroy(DelegateDuration $delegate_duration)
    {
        $deleted = $this->deleteById($delegate_duration->id);

        if ($deleted) {
            $delegate_duration->deleted_by = auth()->user()->id;
            $delegate_duration->save();
        }
    }
}
