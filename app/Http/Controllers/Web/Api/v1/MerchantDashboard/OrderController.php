<?php

namespace App\Http\Controllers\Web\Api\v1\MerchantDashboard;

use App\Models\City;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\MerchantDashboard\Order\OrderResource;
use App\Http\Resources\MerchantDashboard\Order\OrderCollection;
use App\Http\Requests\MerchantDashboard\Order\CreateOrderRequest;
use App\Repositories\Web\Api\v1\MerchantDashboard\OrderRepository;

//use App\Http\Requests\Voucher\UpdateVoucherRequest;

class OrderController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->middleware('can:view,order')->only('show');
        $this->middleware('can:delete,order')->only('destroy');
        $this->orderRepository = $orderRepository;
    }

    public function index()
    {
        $orders = Order::with(['order_items','sender_city','receiver_city','sender_zone','receiver_zone'])
            ->where('merchant_id', auth()->user()->id)
            ->filter(request()->only([
                'receiver_name', 'receiver_phone', 'receiver_address', 'remark', 'thirdparty_invoice',
                'start_date','end_date','good_agent_id'
            ]))->orderBy('id','desc');

        if (request()->has('paginate')) {
            $orders = $orders->paginate(request()->get('paginate'));
        } else {
            $orders = $orders->get();
        }

        return new OrderCollection($orders);
    }

    public function store(CreateOrderRequest $request)
    {
        $request['platform'] = 'GoodApp';
        $order = $this->orderRepository->create($request->all());
        return new OrderResource($order->load(['order_items']));
    }

    public function show(Order $order)
    {
        return new OrderResource($order->load(['order_items']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request, Order $order)
    {
        $order = $this->orderRepository->update_status($order, $request->all());

        return new OrderResource($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        if (!$order->status) {
            $this->orderRepository->destroy($order);

            return response()->json(['status' => 1], Response::HTTP_OK);
        }
        return response()->json(['status' => 2, 'message' => "Unauthenticated!"], Response::HTTP_OK);
    }

    
}
