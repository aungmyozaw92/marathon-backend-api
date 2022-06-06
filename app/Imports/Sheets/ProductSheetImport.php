<?php
namespace App\Imports\Sheets;


use App\Models\Product;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductSheetImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (request()->get('Product') == 'Product') {
            Product::create([
            'merchant_id' => $row['merchant_id'],
            'sku' => isset($row['sku'])?$row['sku']:null,
            'uuid' => Str::orderedUuid(),
            'item_name' => $row['item_name'],
            'item_price' => isset($row['item_price'])?$row['item_price']: 0,
            'is_seasonal' => isset($row['is_seasonal'])?$row['is_seasonal']: 0,
            'is_feature' => isset($row['is_feature'])?$row['is_feature']: 0,
            'lwh' => isset($row['lwh'])?$row['lwh']: 20,
            'weight' => isset($row['weight'])?$row['weight']: 2,
            'created_by_type' => 'Staff',
            'created_by_id' => auth()->user()->id,
            'product_type_id' => isset($row['product_type_id']) ? $row['product_type_id'] : null,
        ]);
        }
    }
}
