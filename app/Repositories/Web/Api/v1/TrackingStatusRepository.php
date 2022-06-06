<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\TrackingStatus;
use App\Repositories\BaseRepository;

class TrackingStatusRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return TrackingStatus::class;
    }

    /**
     * @param array $data
     *
     * @return TrackingStatus
     */
    public function create(array $data) : TrackingStatus
    {
        $status_mm = getConvertedString($data['status_mm']);

        return TrackingStatus::create([
            'status' => $data['status'],
            'status_mm' => $status_mm,
            'status_en' => $data['status_en'],
            'description' => isset($data['description'])?$data['description']:null,
            'description_mm' => isset($data['description_mm'])?$data['description_mm']:null,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param CallStatus  $callStatus
     * @param array $data
     *
     * @return mixed
     */
    public function update(TrackingStatus $trackingStatus, array $data) : TrackingStatus
    {
        $trackingStatus->status = $data['status'];
        $trackingStatus->status_en = $data['status_en'];
        $trackingStatus->status_mm = getConvertedString($data['status_mm']);
        $trackingStatus->description = isset($data['description'])?$data['description']:$trackingStatus->description;
        $trackingStatus->description_mm = isset($data['description_mm'])?$data['description_mm']:$trackingStatus->description_mm;

        if($trackingStatus->isDirty()) {
            $trackingStatus->updated_by = auth()->user()->id;
            $trackingStatus->save();
        }

        return $trackingStatus->refresh();
    }

    /**
     * @param TrackingStatus $trackingStatus
     */
    public function destroy(TrackingStatus $trackingStatus)
    {
        $deleted = $this->deleteById($trackingStatus->id);

        if ($deleted) {
            $trackingStatus->deleted_by = auth()->user()->id;
            $trackingStatus->save();
        }
    }
}

