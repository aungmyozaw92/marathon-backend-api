<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\DeliveryStatus;
use App\Repositories\BaseRepository;

class DeliveryStatusRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return DeliveryStatus::class;
    }

    /**
     * @param array $data
     *
     * @return DeliveryStatus
     */
    public function create(array $data) : DeliveryStatus
    {
        $status_mm = getConvertedString($data['status_mm']);

        return DeliveryStatus::create([
            'status' => $data['status'],
            'status_mm' => $status_mm,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param DeliveryStatus  $deliveryStatus
     * @param array $data
     *
     * @return mixed
     */
    public function update(DeliveryStatus $deliveryStatus, array $data) : DeliveryStatus
    {
        $deliveryStatus->status = $data['status'];
        $deliveryStatus->status_mm = $data['status_mm'];

        if($deliveryStatus->isDirty()) {
            $deliveryStatus->updated_by = auth()->user()->id;
            $deliveryStatus->save();
        }

        return $deliveryStatus->refresh();
    }

    /**
     * @param DeliveryStatus $deliveryStatus
     */
    public function destroy(DeliveryStatus $deliveryStatus)
    {
        $deleted = $this->deleteById($deliveryStatus->id);

        if ($deleted) {
            $deliveryStatus->deleted_by = auth()->user()->id;
            $deliveryStatus->save();
        }
    }
}

