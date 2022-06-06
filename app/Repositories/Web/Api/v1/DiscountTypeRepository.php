<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\DiscountType;
use App\Repositories\BaseRepository;

class DiscountTypeRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return DiscountType::class;
    }

    /**
     * @param array $data
     *
     * @return DiscountType
     */
    public function create(array $data): DiscountType
    {
        return DiscountType::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'created_by' => auth()->user()->id,
        ]);
    }

    /**
     * @param DiscountType $discount_type
     * @param array        $data
     *
     * @return mixed
     */
    public function update(DiscountType $discount_type, array $data): DiscountType
    {
        $discount_type->name = $data['name'];
        $discount_type->description = $data['description'];

        if ($discount_type->isDirty()) {
            $discount_type->updated_by = auth()->user()->id;
            $discount_type->save();
        }

        return $discount_type->refresh();
    }

    /**
     * @param DiscountType $discount_type
     */
    public function destroy(DiscountType $discount_type)
    {
        $deleted = $this->deleteById($discount_type->id);

        if ($deleted) {
            $discount_type->deleted_by = auth()->user()->id;
            $discount_type->save();
        }
    }
}
