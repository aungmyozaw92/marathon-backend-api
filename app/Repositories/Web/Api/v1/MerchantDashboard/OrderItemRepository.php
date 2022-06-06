<?php

namespace App\Repositories\Web\Api\v1\MerchantDashboard;

use App\Models\OrderItem;
use App\Repositories\BaseRepository;

class OrderItemRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return OrderItem::class;
    }

    /**
     * @param array $data
     *
     * @return OrderItem
     */
    public function create(array $data): OrderItem
    {
        $order_item = OrderItem::create([
            'order_id' => $data['order_id'],
            'name' => $data['name'],
            'qty' => $data['qty'],
            'price' => $data['price'],
            'weight' => $data['weight'],
            'product_id' =>  isset($data['product_id']) ? $data['product_id'] : null,
           
            
        ]);
        return $order_item;
    }
}
