<?php

namespace App\Repositories\Mobile\Api\v1;

use App\Models\PaymentType;
use App\Repositories\BaseRepository;

class PaymentTypeRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return PaymentType::class;
    }

    /**
     * @param array $data
     *
     * @return PaymentType
     */
    public function create(array $data) : PaymentType
    {
        return PaymentType::create([
            'name'        => $data['name'],
            'name_mm'     => $data['name_mm'],
            'description' => isset($data['description'])? $data['description']:null,
            'default'     => isset($data['default']) ? $data['default'] : null,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param PaymentType  $payment_type
     * @param array $data
     *
     * @return mixed
     */
    public function update(PaymentType $payment_type, array $data) : PaymentType
    {
        $payment_type->name        = $data['name'];
        $payment_type->name_mm     = $data['name_mm'];
        $payment_type->description = isset($data['description']) ? $data['description'] : $payment_type->description;
        $payment_type->default     = isset($data['default']) ? $data['default'] : $payment_type->default;

        if ($payment_type->isDirty()) {
            $payment_type->updated_by = auth()->user()->id;
            $payment_type->save();
        }

        return $payment_type->refresh();
    }

    /**
     * @param PaymentType $payment_type
     */
    public function destroy(PaymentType $payment_type)
    {
        $deleted = $this->deleteById($payment_type->id);

        if ($deleted) {
            $payment_type->deleted_by = auth()->user()->id;
            $payment_type->save();
        }
    }
}
