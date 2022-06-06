<?php

namespace App\Repositories\Web\Api\v1\MerchantDashboard;

use App\Models\Product;
use App\Models\Attachment;
use Illuminate\Support\Str;
use App\Models\ProductReview;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Storage;
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
    public function create(array $data): Product
    {
        $product =  Product::create([
            'uuid'        => Str::orderedUuid(),
            'item_name'   => getConvertedString($data['item_name']),
            'item_price'  => $data['item_price'],
            'lwh'  => isset($data['lwh']) ? $data['lwh'] : 20,
            'weight'  => isset($data['weight']) ? $data['weight'] : 2,
            'is_seasonal'  => isset($data['is_seasonal']) ? $data['is_seasonal'] : 0,
            'is_feature'  => isset($data['is_feature']) ? $data['is_feature'] : 0,
            'product_type_id'  => isset($data['product_type_id']) ? $data['product_type_id'] : null,
            'sku'  => isset($data['sku']) ? $data['sku'] : null,
            'merchant_id' => auth()->user()->id,
            'created_by_id'  => auth()->user()->id,
            'created_by_type'  => 'Merchant',
        ]);
        if (isset($data['file'])) {
            $attachmentRepository = new AttachmentRepository();
            $attachmentRepository->create_attachment($product, $data);
        }
        return $product;
    }

    /**
     * @param Product  $product
     * @param array $data
     *
     * @return mixed
     */

    public function update(Product $product, array $data): Product
    {
        $product->item_name = getConvertedString($data['item_name']);
        $product->item_price = isset($data['item_price']) ? $data['item_price'] : $product->item_price;
        // $product->merchant_id = isset($data['merchant_id'])? $data['merchant_id'] : $product->merchant_id;
        $product->product_type_id = isset($data['product_type_id']) ? $data['product_type_id'] : $product->product_type_id;
        $product->is_seasonal = isset($data['is_seasonal']) ? $data['is_seasonal'] : $product->is_seasonal;
        $product->is_feature = isset($data['is_feature']) ? $data['is_feature'] : $product->is_feature;
        $product->lwh = isset($data['lwh']) ? $data['lwh'] : $product->lwh;
        $product->weight = isset($data['weight']) ? $data['weight'] : $product->weight;
        $product->sku = isset($data['sku']) ? $data['sku'] : $product->sku;

        if ($product->isDirty()) {
            $product->updated_by_id = auth()->user()->id;
            $product->updated_by_type = 'Merchant';
            $product->save();
        }

        if (isset($data['file'])) {
            $product->attachments()->delete();
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
            $product->deleted_by_type = 'Merchant';
            $product->save();

            foreach ($product->attachments as $attachment) {
                $attachmentRepository = new AttachmentRepository();
                $attachmentRepository->destroy($attachment);
            }
        }
    }


    public function create_review(array $data)
    {
        $product_review =  ProductReview::create([
            'note'   => isset($data['note']) ? getConvertedString($data['note']) : null,
            'rating'  => $data['rating'],
            'customer_id'  => $data['customer_id'],
            'product_id'  => $data['product_id'],
        ]);
        if (isset($data['file'])) {
            $this->upload_review_file($product_review, $data);
        }
        return $product_review->refresh();
    }

    public function update_product_review(ProductReview $product_review, array $data): ProductReview
    {
        $product_review->rating = isset($data['rating']) ? $data['rating'] : $product_review->rating;
        // $product_review->product_id = isset($data['product_id'])? $data['product_id'] : $product_review->product_id;

        if ($product_review->isDirty()) {
            $product_review->save();
        }
        return $product_review->refresh();
    }

    public function upload_review_file($product_review, $data)
    {
        $file_name = null;
        $file = $data['file'];
        $folder  = 'product_review';
        $date_folder = date('F-Y');
        $path = $folder . '/' . $date_folder;
        if (gettype($file) == 'string') {
            $file_name = 'product_review' . $product_review->id . '_image_' . time() . '.' . 'png';
            Storage::disk('dospace')->put($path . '/' . $file_name, base64_decode($file));
        } else {
            $file_name = 'product_review' . $product_review->id . '_image_' . time() . '_' . $file->getClientOriginalName();
            $content = file_get_contents($file);
            Storage::disk('dospace')->put($path . '/' . $file_name, $content);
        }
        Storage::setVisibility($path . '/' . $file_name, "public");
        Attachment::create([
            'resource_type' => 'ProductReview',
            'image' => $file_name,
            'resource_id' => $product_review->id,
            'note' => $product_review->note,
            'latitude' => null,
            'longitude' => null,
            'is_sign' => 0,
            'created_by' => 1
        ]);
    }

    /**
     * @param ProductReview $product
     */
    public function destroy_product_review(ProductReview $product_review)
    {
        $deleted = $product_review->delete($product_review->id);
        if ($deleted) {
            $product_review->save();
        }
    }
}
