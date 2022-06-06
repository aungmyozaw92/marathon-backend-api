<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\StoreStatus;
use App\Repositories\BaseRepository;

class StoreStatusRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return StoreStatus::class;
    }

    /**
     * @param array $data
     *
     * @return StoreStatus
     */
    public function create(array $data) : StoreStatus
    {
        //if (isset($data['status_mm'])) {
            $status_mm = getConvertedString($data['status_mm']);
        //}

        return StoreStatus::create([
            'status' => $data['status'],
            'status_mm' => $status_mm,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param StoreStatus  $storeStatus
     * @param array $data
     *
     * @return mixed
     */
    public function update(StoreStatus $storeStatus, array $data) : StoreStatus
    {
        $status_mm = getConvertedString($data['status_mm']);

        $storeStatus->status = $data['status'];
        $storeStatus->status_mm = $status_mm;

        if($storeStatus->isDirty()) {
            $storeStatus->updated_by = auth()->user()->id;
            $storeStatus->save();
        }

        return $storeStatus->refresh();
    }

    /**
     * @param StoreStatus $storeStatus
     */
    public function destroy(StoreStatus $storeStatus)
    {
        $deleted = $this->deleteById($storeStatus->id);

        if ($deleted) {
            $storeStatus->deleted_by = auth()->user()->id;
            $storeStatus->save();
        }
    }
}

