<?php

namespace App\Repositories\Web\Api\v1\MerchantDashboard;

use App\Models\Inventory;
use App\Repositories\BaseRepository;

class InventoryRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Inventory::class;
    }

    /**
     * @param array $data
     *
     * @return Inventory
     */
    public function create(array $data) : Inventory
    {
        return Inventory::create([
            'product_id' => $data['product_id'], 
            'minimum_stock' => isset($data['minimum_stock'])? $data['minimum_stock'] : 0, 
            'qty' => $data['qty'], 
            'purchase_price' => isset($data['purchase_price'])? $data['purchase_price'] : null, 
            'sale_price' => $data['sale_price'], 
            'is_refundable' => isset($data['is_refundable'])? $data['is_refundable'] : 0, 
            'is_taxable' => isset($data['is_taxable'])? $data['is_taxable'] : 0, 
            'is_fulfilled_by' => isset($data['is_fulfilled_by'])? $data['is_fulfilled_by'] : 0, 
            'vendor_name' => isset($data['vendor_name'])? $data['vendor_name'] : null, 
            'created_by_id' => auth()->user()->id,
            'created_by_type' => 'Merchant',
        ]);
    }

    /**
     * @param Inventory  $inventory
     * @param array $data
     *
     * @return mixed
     */
    public function update(Inventory $inventory, array $data) : Inventory
    {
        // $inventory->product_id = isset($data['product_id']) ? $data['product_id'] : $inventory->product_id;
        $inventory->minimum_stock = isset($data['minimum_stock']) ? $data['minimum_stock'] : $inventory->minimum_stock;
        // $inventory->qty = isset($data['qty']) ? $data['qty'] : $inventory->qty;
        $inventory->purchase_price = isset($data['purchase_price']) ? $data['purchase_price'] : $inventory->purchase_price;
        $inventory->sale_price = isset($data['sale_price']) ? $data['sale_price'] : $inventory->sale_price;
        $inventory->is_refundable = isset($data['is_refundable']) ? $data['is_refundable'] : $inventory->is_refundable;
        $inventory->is_taxable = isset($data['is_taxable']) ? $data['is_taxable'] : $inventory->is_taxable;
        $inventory->is_fulfilled_by = isset($data['is_fulfilled_by']) ? $data['is_fulfilled_by'] : $inventory->is_fulfilled_by;
        $inventory->vendor_name = isset($data['vendor_name']) ? $data['vendor_name'] : $inventory->vendor_name;

        if($inventory->isDirty()) {
            $inventory->updated_by_id = auth()->user()->id;
            $inventory->updated_by_type = 'Merchant';
            $inventory->save();
        }

        return $inventory->refresh();
    }

    public function add_qty(Inventory $inventory, array $data) : Inventory
    {
        $inventory->qty = $inventory->qty + $data['qty'];
        if($inventory->isDirty()) {
            $inventory->updated_by_id = auth()->user()->id;
            $inventory->updated_by_type = 'Merchant';
            $inventory->save();
        }

        return $inventory->refresh();
    }

    /**
     * @param Inventory $inventory
     */
    public function destroy(Inventory $inventory)
    {
        $deleted = $this->deleteById($inventory->id);

        if ($deleted) {
            $inventory->deleted_by_id = auth()->user()->id;
            $inventory->deleted_by_type = 'Merchant';
            $inventory->save();
        }
    }
}

