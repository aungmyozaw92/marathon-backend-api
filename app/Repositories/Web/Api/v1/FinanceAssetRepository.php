<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\FinanceAsset;
use App\Repositories\BaseRepository;

class FinanceAssetRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return FinanceAsset::class;
    }

    /**
     * @param array $data
     *
     * @return FinanceAsset
     */
    public function create(array $data) : FinanceAsset
    {
        return FinanceAsset::create([
            'name' => $data['name'],
            'branch_id' => isset($data['branch_id']) ? $data['branch_id'] : null,
            'asset_type_id' => $data['asset_type_id'],
            'depreciation_expense_account_id' => $data['depreciation_expense_account_id'],
            'accumulated_depreciation_account_id' => $data['accumulated_depreciation_account_id'],
            'description' => isset($data['description']) ? $data['description'] : null,
            'serial_no' => isset($data['serial_no']) ? $data['serial_no'] : null,
            'purchase_price' => $data['purchase_price'],
            'purchase_date' => $data['purchase_date'],
            'depreciation_start_date' => $data['depreciation_start_date'],
            'warranty_month' => $data['warranty_month'],
            'depreciation_month' => $data['depreciation_month'],
            'depreciation_rate' => $data['depreciation_rate'],
            'created_by' => auth()->user()->id
        ]);
    }

    /**
     * @param FinanceAsset  $finance_asset
     * @param array $data
     *
     * @return mixed
     */
    public function update(FinanceAsset $finance_asset, array $data) : FinanceAsset
    {
        $finance_asset->name = isset($data['name']) ? $data['name'] : $finance_asset->name;
        $finance_asset->branch_id = isset($data['branch_id']) ? $data['branch_id'] : $finance_asset->branch_id;
        $finance_asset->asset_type_id = isset($data['asset_type_id']) ? $data['asset_type_id'] : $finance_asset->asset_type_id;
        $finance_asset->description = isset($data['description']) ? $data['description'] : $finance_asset->description;
        $finance_asset->serial_no = isset($data['serial_no']) ? $data['serial_no'] : $finance_asset->serial_no;
        $finance_asset->purchase_price = isset($data['purchase_price']) ? $data['purchase_price'] : $finance_asset->purchase_price;
        $finance_asset->purchase_date = isset($data['purchase_date']) ? $data['purchase_date'] : $finance_asset->purchase_date;
        $finance_asset->depreciation_start_date = isset($data['depreciation_start_date']) ? $data['depreciation_start_date'] : $finance_asset->depreciation_start_date;
        $finance_asset->warranty_month = isset($data['warranty_month']) ? $data['warranty_month'] : $finance_asset->warranty_month;
        $finance_asset->depreciation_month = isset($data['depreciation_month']) ? $data['depreciation_month'] : $finance_asset->depreciation_month;
        $finance_asset->depreciation_rate = isset($data['depreciation_rate']) ? $data['depreciation_rate'] : $finance_asset->depreciation_rate;
        $finance_asset->depreciation_expense_account_id = isset($data['depreciation_expense_account_id']) ? $data['depreciation_expense_account_id'] : $finance_asset->depreciation_expense_account_id;
        $finance_asset->accumulated_depreciation_account_id = isset($data['accumulated_depreciation_account_id']) ? $data['accumulated_depreciation_account_id'] : $finance_asset->accumulated_depreciation_account_id;
        
        if($finance_asset->isDirty()) {
            $finance_asset->updated_by = auth()->user()->id;
            $finance_asset->save();
        }

        return $finance_asset->refresh();
    }

    /**
     * @param FinanceAsset $finance_asset
     */
    public function destroy(FinanceAsset $finance_asset)
    {
        $deleted = $this->deleteById($finance_asset->id);

        if ($deleted) {
            $finance_asset->deleted_by = auth()->user()->id;
            $finance_asset->save();
        }
    }
}

