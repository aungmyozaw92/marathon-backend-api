<?php

namespace App\Repositories\Web\Api\v1\MerchantDashboard;

use App\Models\Order;
use App\Repositories\BaseRepository;
use App\Repositories\Web\Api\v1\MerchantDashboard\OrderItemRepository;

class OrderRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Order::class;
    }

    /**
     * @param array $data
     *
     * @return Order
     */
    public function create(array $data): Order
    {
       $user = auth()->user();
       $default_branch = $user->merchant_associates->where('is_default', true)->first();
       if(isset($data['sender_city_id'])){
           $sender_city_id = $data['sender_city_id'];
       }else{
           $sender_city_id = $default_branch->city_id;
       }
       if(isset($data['sender_zone_id'])){
           $sender_zone_id = $data['sender_zone_id'];
           
       }else{
           $sender_zone_id = $default_branch->zone_id;
       }

       $is_paid = false;

       if ($data['payment_method'] === 'Pre-Paid') {
            $is_paid = true;
       }
       
        $payment_type_id = 2;
        $order = Order::create([
            'merchant_id' => $user->id,
            'receiver_name' => $data['receiver_name'],
            'receiver_phone' => $data['receiver_phone'],
            'receiver_address' => $data['receiver_address'],
            'receiver_email' =>  isset($data['receiver_email']) ? $data['receiver_email'] : null,
            'sender_city_id' => $sender_city_id,
            'sender_zone_id' => $sender_zone_id,
            'receiver_city_id' => $data['receiver_city_id'],
            'receiver_zone_id' => $data['receiver_zone_id'],
            'payment_type_id' => $payment_type_id,
            'global_scale_id' => isset($data['global_scale_id']) ? $data['global_scale_id'] : 1,
            'remark' => isset($data['remark']) ? $data['remark'] : null,
            'thirdparty_invoice' => isset($data['thirdparty_invoice']) ? $data['thirdparty_invoice'] : null,
            'good_agent_id' => isset($data['good_agent_id']) ? $data['good_agent_id'] : null,
            'total_weight' => $data['total_weight'],
            'total_qty' => $data['total_qty'],
            'total_price' => $data['total_price'],
            'payment_option' => $data['payment_option'],
            'payment_method' => $data['payment_method'],
            'is_paid' => $is_paid,
            'total_delivery_amount' => isset($data['total_delivery_amount']) ? $data['total_delivery_amount'] : 0,
            'platform' => $data['platform'],
            
        ]);
        
        $orderItemRepository = new OrderItemRepository();
        if (isset($data['order_items'])) {
            foreach ($data['order_items'] as $key => $items) {
                $items['order_id'] = $order->id;
                $orderItemRepository->create($items);
            }
        }
        return $order;
    }

    /**
     * @param Order $order
     * @param array    $data
     *
     * @return mixed
     */
    public function update_status(Order $order, array $data): Order
    {
        $order->is_receive = 1;
        $order->is_paid = 1;
        if ($order->isDirty()) {
            // $order->updated_by_type = "Merchant";
            // $order->updated_by = auth()->user()->id;
            $order->save();
        }

        return $order->refresh();
    }

    /**
     * @param Order $order
     */
    public function destroy(Order $order)
    {
        $deleted = $this->deleteById($order->id);

        if ($deleted) {
            $order->save();
        }
    }
}
