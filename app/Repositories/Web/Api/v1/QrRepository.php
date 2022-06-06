<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Qr;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\QrAssociateRepository;

class QrRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Qr::class;
    }

    /**
     * @param array $data
     *
     * @return Qr
     */
    public function create(array $data) : Qr
    {
        if ($data['actor_type'] == 'Merchant') {
            $actor_id = $data['merchant_id'];            # code...
        }else if ($data['actor_type'] == 'Customer') {
            $actor_id = $data['customer_id'];            
        }else{
            $actor_id = $data['agent_id'];
        }
        
        $qr = Qr::create([
            'qty' => $data['qty'],
            'actor_id' => $actor_id,
            'actor_type' => $data['actor_type'],            
            'created_by' => auth()->user()->id,
        ]);

        $qrAssociateRepository = new QrAssociateRepository();
        $qrAssociateRepository->create($qr);

        return $qr;
    }

    /**
     * @param Qr $qr
     */
    public function destroy(Qr $qr)
    {
        $deleted = $this->deleteById($qr->id);

        if ($deleted) {
            $qr->deleted_by = auth()->user()->id;
            $qr->save();
        }
    }
}
