<?php

namespace App\Repositories\Web\Api\v1;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\MerchantDashboard\AttachmentRepository;

class ProductRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Product::class;
    }

    /**
     * @param array $data
     *
     * @return Product
     */
    public function create(array $data) : Product
    {
        $product =  Product::create([
            'uuid'        => Str::orderedUuid(),
            'item_name'   => getConvertedString($data['item_name']),
            'item_price'  => $data['item_price'],
            'merchant_id' => $data['merchant_id'],
            'lwh'  => isset($data['lwh'])? $data['lwh']: 20,
            'weight'  => isset($data['weight'])? $data['weight']: 2,
            'is_seasonal'  => isset($data['is_seasonal'])? $data['is_seasonal']: 0,
            'is_feature'  => isset($data['is_feature'])? $data['is_feature']: 0 ,
            'product_type_id'  => isset($data['product_type_id'])? $data['product_type_id']: null,
            'sku'  => isset($data['sku'])? $data['sku']: null,
            
            'created_by_id'  => auth()->user()->id,
            'created_by_type'  => 'Staff',
        ]);
        if (isset($data['file'])) {
            $attachmentRepository = new AttachmentRepository();
            $attachmentRepository->create_attachment($product, $data);
        }
        return $product->refresh();
    }

    /**
     * @param Product  $product
     * @param array $data
     *
     * @return mixed
     */
    public function update(Product $product, array $data) : Product
    {
        $product->item_name = getConvertedString($data['item_name']);
        $product->item_price = isset($data['item_price'])? $data['item_price'] : $product->item_price;
        $product->merchant_id = isset($data['merchant_id'])? $data['merchant_id'] : $product->merchant_id;
        $product->product_type_id = isset($data['product_type_id'])? $data['product_type_id'] : $product->product_type_id;
        $product->is_seasonal = isset($data['is_seasonal'])? $data['is_seasonal'] : $product->is_seasonal;
        $product->is_feature = isset($data['is_feature'])? $data['is_feature'] : $product->is_feature;
        $product->lwh = isset($data['lwh'])? $data['lwh'] : $product->lwh;
        $product->weight = isset($data['weight'])? $data['weight'] : $product->weight;
        $product->sku = isset($data['sku'])? $data['sku'] : $product->sku;

        if ($product->isDirty()) {
            $product->updated_by_id = auth()->user()->id;
            $product->updated_by_type = 'Staff';
            $product->save();
        }

        if (isset($data['file'])) {
            $attachmentRepository = new AttachmentRepository();
            $attachmentRepository->create_attachment($product, $data);
        }
        return $product->refresh();
    }

    /**
     * @param Product $product
     */
    public function destroy(Product $product)
    {
        $deleted = $this->deleteById($product->id);

        if ($deleted) {
            $product->deleted_by_id = auth()->user()->id;
            $product->deleted_by_type = 'Staff';
            $product->save();

            foreach ($product->attachments as $attachment) {
                $attachmentRepository = new AttachmentRepository();
                $attachmentRepository->destroy($attachment);
            }
        }
    }
}
