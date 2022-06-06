<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\QrAssociate;
use App\Repositories\BaseRepository;

class QrAssociateRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return QrAssociate::class;
    }

    /**
     * @param array $data
     *
     * @return QrAssociate
     */
    public function create($data)
    {
        $qr_associate_id = 0;
        if (QrAssociate::count()) {
            $qr_associate_id = QrAssociate::latest('id')->first()->id;
        }
        for ($i=1; $i <= $data['qty']; $i++) {
            $qr_associate_id += 1;
            $hex_code = 'm'. hexdec($qr_associate_id);
            $md_code = md5($hex_code);

            $qr_associate = QrAssociate::create([
                'qr_id'             => $data['id'],
                'qr_code'          => $hex_code.substr($md_code, 0, 2),
                'created_by'        => auth()->user()->id,
            ]);
            $qr_associate_id = $qr_associate->id;
        }
        return $qr_associate;
    }

    /**
     * @param Qr $qr_associate
     */
    public function destroy(QrAssociate $qr_associate)
    {
        $deleted = $this->deleteById($qr_associate->id);

        if ($deleted) {
            $qr_associate->deleted_by = auth()->user()->id;
            $qr_associate->save();
        }
    }
}
