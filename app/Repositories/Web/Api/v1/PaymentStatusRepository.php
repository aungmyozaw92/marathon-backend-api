<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\PaymentStatus;
use App\Repositories\BaseRepository;

class PaymentStatusRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return PaymentStatus::class;
    }

    /**
     * @param array $data
     *
     * @return PaymentStatus
     */
    public function create(array $data) : PaymentStatus
    {
        //if (isset($data['name_mm'])) {
            $name_mm = getConvertedString($data['name_mm']);
       // }
       if (isset($data['description'])) {
            $description = getConvertedString($data['description']);
        }


        return PaymentStatus::create([
            'name' => $data['name'],
            'name_mm' => $name_mm,
            'description' => isset($data['description'])? $description : null,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param PaymentStatus  $PaymentStatus
     * @param array $data
     *
     * @return mixed
     */
    public function update(PaymentStatus $payment_status, array $data) : PaymentStatus
    {
        if (isset($data['description'])) {
            $description = getConvertedString($data['description']);
        }
        $name_mm = getConvertedString($data['name_mm']);

        $payment_status->name = $data['name'];
        $payment_status->name_mm = $name_mm;
        $payment_status->description = isset($data['description'])? $description : null;

        if ($payment_status->isDirty()) {
            $payment_status->updated_by = auth()->user()->id;
            $payment_status->save();
        }

        return $payment_status->refresh();
    }

    /**
     * @param PaymentStatus $payment_status
     */
    public function destroy(PaymentStatus $payment_status)
    {
        $deleted = $this->deleteById($payment_status->id);

        if ($deleted) {
            $payment_status->deleted_by = auth()->user()->id;
            $payment_status->save();
        }
    }
}
