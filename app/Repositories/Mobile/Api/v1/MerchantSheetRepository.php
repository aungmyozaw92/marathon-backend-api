<?php

namespace App\Repositories\Mobile\Api\v1;

use App\Models\MerchantSheet;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\TransactionRepository;

class MerchantSheetRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return MerchantSheet::class;
    }

    public function create_withdraw(array $data)
    {
        $transactionRepository = new TransactionRepository();
        $data['from_account_type'] = 'HQ';
        $data['to_account_id'] = auth()->user()->id;
        $responses = $transactionRepository->create_withdraw($data);

        return $responses;
    }

}
