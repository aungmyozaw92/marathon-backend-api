<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Coupon;
use App\Models\CouponAssociate;
use App\Repositories\BaseRepository;

class CouponRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Coupon::class;
    }

    /**
     * @param array $data
     *
     * @return Coupon
     */
    public function create(array $data) : Coupon
    {
        $coupon = Coupon::create([
            'discount_type_id' => $data['discount_type_id'],
            'valid_date' => $data['valid_date'],
            'amount' => $data['amount'],
            'created_by' => auth()->user()->id
        ]);

        $associate = [
                'qty' => isset($data['qty'])?$data['qty']:1,
                'coupon_id' => $coupon->id,
                'valid' => 0
            ];
        $couponAssociateRepository = new CouponAssociateRepository();
        $couponAssociate = $couponAssociateRepository->create($associate);

        return $coupon->refresh();
    }

    /**
     * @param Coupon  $coupon
     * @param array $data
     *
     * @return mixed
     */
    public function update(Coupon $coupon, array $data) : Coupon
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
     * @param Coupon $coupon
     */
    public function destroy(Coupon $coupon)
    {
        $deleted = $this->deleteById($coupon->id);

        if ($deleted) {
            $coupon->deleted_by = auth()->user()->id;
            $coupon->save();
        }
    }

    public function valid_coupon_code(array $data)
    {
        $code = isset($data['coupon_code'])?$data['coupon_code']:null;
        $coupon_associate = CouponAssociate::where('code', $code)->where('valid', 1)->first();
        $coupon = ($coupon_associate) ? $coupon_associate->coupon()->validDate()->first() : null;
        if ($coupon) {
            $coupon["associate_id"] = $coupon_associate->id;
            return $coupon;
        } else {
            return null;
        }
    }

    // public function valid_check_coupon_code(array $data)
    // {
    //     $code = isset($data['coupon_code'])?$data['coupon_code']:null;
    //     $coupon_associate = CouponAssociate::where('code', $code)->where('valid', 1)->first();
    //     $coupon = ($coupon_associate) ? $coupon_associate->coupon()->validDate()->first() : null;
    //     return $coupon;
    // }
}
