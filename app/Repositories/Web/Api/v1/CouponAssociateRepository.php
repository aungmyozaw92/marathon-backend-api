<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\CouponAssociate;
use App\Repositories\BaseRepository;

class CouponAssociateRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return CouponAssociate::class;
    }

    /**
     * @param array $data
     *
     * @return CouponAssociate
     */
    public function create(array $data) : CouponAssociate
    {
        for ($i=0; $i < $data['qty']; $i++) {
            $coupon_associate = CouponAssociate::create([
                'coupon_id'  => $data['coupon_id'],
                'valid'      => $data['valid'],
                'code'       =>  str_random(6),
                'created_by' => auth()->user()->id
            ]);
        }

        return $coupon_associate;
    }

    /**
     * @param CouponAssociate  $coupon
     * @param array $data
     *
     * @return mixed
     */
    public function update(CouponAssociate $coupon, array $data) : CouponAssociate
    {
        $coupon->discount_type_id = $data['discount_type_id'];
        $coupon->valid_date = $data['valid_date'];
        $coupon->amount = $data['amount'];

        if ($coupon->isDirty()) {
            $coupon->updated_by = auth()->user()->id;
            $coupon->save();
        }

        return $coupon->refresh();
    }

    /**
     * @param CouAssociatepAssociateon $coupon
     */
    public function destroy(CouponAssociate $coupon)
    {
        $deleted = $this->deleteById($coupon->id);

        if ($deleted) {
            $coupon->deleted_by = auth()->user()->id;
            $coupon->save();
        }
    }
}
