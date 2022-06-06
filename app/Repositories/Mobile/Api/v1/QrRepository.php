<?php

namespace App\Repositories\Mobile\Api\v1;

use App\Models\Qr;
use App\Models\QrAssociate;
use App\Repositories\BaseRepository;

class QrRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return QrAssociate::class;
    }

    /**
     * @param voucher  $voucher
     * @param array $data
     *
     * @return mixed
     */

    public function bindQR($voucher, $qr_associate)
    {
        $voucher->qr_associate_id = $qr_associate->id;
        if ($voucher->isDirty()) {
            $voucher->save();

            $qr_associate->valid = 1;
            $qr_associate->save();
        }
        return $voucher->refresh();
    }

    /**
     * @param voucher  $voucher
     *
     * @return mixed
     */
    public function unBindQR($voucher)
    {   
        $qr_associate = $voucher->qr_associate;
        $voucher->qr_associate_id = null;
        if ($voucher->isDirty()) {
            $voucher->save();

            $qr_associate->valid = 0;
            $qr_associate->save();
        }
        return $voucher->refresh();
    }

    /**
     * @param voucher  $voucher
     * 1234
     * 
     * @return mixed
     */
    public function checkQrCode($qr_code,$voucher = null)
    {   
        $qr_associate = QrAssociate::where('qr_code', $qr_code)->first();
        $responses = ['status' => 2];

        if ($qr_associate && !$qr_associate->valid) {
            if ($qr_associate) {
                $qr = $qr_associate->qr->where('actor_type', 'Merchant')->where('actor_id', auth()->user()->id)->first();
            }

           if ($qr) {
               if ($voucher && $voucher->qr_associate_id) {
                    $responses['message'] = "Voucher already bind";
                }

               $responses = ['status' => 1,'message' => 'QR valid'];
           }else{
                $responses['message'] = 'QR invalid';
           }
        }else{
            $responses['message'] =  'QR invalid.';
        }
        return $responses;
    }

}
