<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\FinanceAssetType;
use App\Repositories\BaseRepository;

class FinanceAssetTypeRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return FinanceAssetType::class;
    }

    /**
     * @param array $data
     *
     * @return FinanceAssetType
     */
    public function create(array $data) : FinanceAssetType
    {
        return FinanceAssetType::create([
            'name' => $data['name'],
            'accumulated_depreciation_account_id' => $data['accumulated_depreciation_account_id'],
            'depreciation_expense_account_id' => $data['depreciation_expense_account_id'],
            'depreciation_rate' => $data['depreciation_rate'],
            'branch_id' => isset($data['branch_id']) ? $data['branch_id'] : null,
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param FinanceAssetType  $finance_asset_type
     * @param array $data
     *
     * @return mixed
     */
    public function update(FinanceAssetType $finance_asset_type, array $data) : FinanceAssetType
    {
        $finance_asset_type->name = isset($data['name']) ? $data['name'] : $finance_asset_type->name;
        $finance_asset_type->accumulated_depreciation_account_id = isset($data['accumulated_depreciation_account_id']) ? $data['accumulated_depreciation_account_id'] : $finance_asset_type->accumulated_depreciation_account_id;
        $finance_asset_type->depreciation_expense_account_id = isset($data['depreciation_expense_account_id']) ? $data['depreciation_expense_account_id'] : $finance_asset_type->depreciation_expense_account_id;
        $finance_asset_type->depreciation_rate = isset($data['depreciation_rate']) ? $data['depreciation_rate'] : $finance_asset_type->depreciation_rate;
        $finance_asset_type->branch_id = isset($data['branch_id']) ? $data['branch_id'] : $finance_asset_type->branch_id;
       
        if($finance_asset_type->isDirty()) {
            $finance_asset_type->updated_by = auth()->user()->id;
            $finance_asset_type->save();
        }

        return $finance_asset_type->refresh();
    }

    /**
     * @param FinanceAssetType $finance_asset_type
     */
    public function destroy(FinanceAssetType $finance_asset_type)
    {
        $deleted = $this->deleteById($finance_asset_type->id);

        if ($deleted) {
            $finance_asset_type->deleted_by = auth()->user()->id;
            $finance_asset_type->save();
        }
    }
}

