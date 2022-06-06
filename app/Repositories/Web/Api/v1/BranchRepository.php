<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Branch;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\AccountRepository;

class BranchRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Branch::class;
    }

    /**
     * @param array $data
     *
     * @return Branch
     */
    public function create(array $data) : Branch
    {
        $branch = Branch::create([
            'name' => $data['name'],
            'city_id' => $data['city_id'],
            'zone_id' => $data['zone_id'],
            'delivery_commission' => isset($data['delivery_commission']) ? $data['delivery_commission'] : 0,
            'created_by' => auth()->user()->id
        ]);

        // if (Branch::count() >= 2) {
        $accountRepository = new AccountRepository();
        $account = [
                'city_id' => $branch->city_id,
                'accountable_type' => 'Branch',
                'accountable_id' => $branch->id,
            ];
        $accountRepository->create($account);
        // }
        return $branch;
    }

    /**
     * @param Branch  $branch
     * @param array $data
     *
     * @return mixed
     */
    public function update(Branch $branch, array $data) : Branch
    {
        $branch->name = isset($data['name']) ? $data['name'] : $branch->name;
        $branch->city_id = isset($data['city_id']) ? $data['city_id'] : $branch->city_id;
        $branch->zone_id = isset($data['zone_id']) ? $data['zone_id'] : $branch->zone_id;
        $branch->delivery_commission = isset($data['delivery_commission']) ? $data['delivery_commission'] : $branch->delivery_commission;

        if ($branch->isDirty()) {
            $branch->updated_by = auth()->user()->id;
            $branch->save();
        }

        if (!$branch->account) {
            $accountRepository = new AccountRepository();
            $account = [
                'city_id' => $branch->city_id,
                'accountable_type' => 'Branch',
                'accountable_id' => $branch->id,
            ];
            $accountRepository->create($account);
        }

        return $branch->refresh();
    }


    /**
     * @param Branch $branch
     */
    public function destroy(Branch $branch)
    {
        $deleted = $this->deleteById($branch->id);

        if ($deleted) {
            $branch->deleted_by = auth()->user()->id;
            $branch->save();
        }
    }
}
