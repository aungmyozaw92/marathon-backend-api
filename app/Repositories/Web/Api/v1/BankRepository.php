<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Bank;
use App\Repositories\BaseRepository;

class BankRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Bank::class;
    }

    /**
     * @param array $data
     *
     * @return Bank
     */
    public function create(array $data) : Bank
    {
        return Bank::create([
            'name' => $data['name']
        ]);
    }

    /**
     * @param Bank  $bank
     * @param array $data
     *
     * @return mixed
     */
    public function update(Bank $bank, array $data) : Bank
    {
        $bank->name = $data['name'];

        if ($bank->isDirty()) {
            $bank->save();
        }

        return $bank->refresh();
    }


    /**
     * @param Bank $bank
     */
    public function destroy(Bank $bank)
    {
        $deleted = $this->deleteById($bank->id);

        if ($deleted) {
            $bank->save();
        }
    }
}
