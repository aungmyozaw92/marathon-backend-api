<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\FailureStatus;
use App\Repositories\BaseRepository;

class FailureStatusRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return FailureStatus::class;
    }

    /**
     * @param array $data
     *
     * @return FailureStatus
     */
    public function create(array $data): FailureStatus
    {
        return FailureStatus::create([
            'category'       => $data['category'],
            'specification'  => getConvertedString($data['specification'])
        ]);
    }

    /**
     * @param FailureStatus  $FailureStatus
     * @param array $data
     *
     * @return mixed
     */
    public function update(FailureStatus $failureStatus, array $data): FailureStatus
    {
        $failureStatus->category = $data['category'];
        $failureStatus->specification = getConvertedString($data['specification']);
        if ($failureStatus->isDirty()) {
            // $failureStatus->updated_by = auth()->user()->id;
            $failureStatus->save();
        }

        return $failureStatus->refresh();
    }

    /**
     * @param FailureStatus $FailureStatus
     */
    public function destroy(FailureStatus $failureStatus)
    {
        $deleted = $this->deleteById($failureStatus->id);

        if ($deleted) {
            // $failureStatus->deleted_by = auth()->user()->id;
            $failureStatus->save();
        }
    }
}
