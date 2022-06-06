<?php

namespace App\Events;

use App\Models\DeliSheetHistory;
use App\Models\MerchantSheetHistory;
use App\Models\WaybillHistory;
use App\Models\Voucher;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\VoucherHistory;
use App\Models\PickupHistory;
use App\Models\ReturnSheetHistory;

class LogHistoryEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
    public function voucherInSheet($data)
    {
        $attributes = $this->getAttr($data);
        $voucher_history = new VoucherHistory($attributes);
        auth()->user()->voucher_histories()->save($voucher_history);
    }

    public function voucherInPickup($data)
    {
        $attributes = $this->getAttr($data);
        $pickup_history = new PickupHistory($attributes);
        auth()->user()->pickup_histories()->save($pickup_history);
    }

    public function pickupLogVoucher($data)
    {
        $attributes = $this->getAttr($data);
        $voucher_history = VoucherHistory::create($attributes);
        auth()->user()->voucher_histories()->save($voucher_history);
    }

    public function getAttr(array $request)
    {
        $logStatusId = getStatusId($request['requests']['logStatus']);
        $attributes = $request['requests'];
        $attributes['log_status_id'] = $logStatusId;
        $attributes['created_by'] = isset(auth()->user()->id) ? auth()->user()->id : 1;
        return $attributes;
    }


    public function deliSheetForVoucher($data)
    {
        $attributes = $this->getAttr($data);
        DeliSheetHistory::create($attributes);
    }
    public function waybillForVoucher($data)
    {
        $attributes = $this->getAttr($data);
        $waybill_history = WaybillHistory::create($attributes);
        auth()->user()->waybill_histories()->save($waybill_history);
    }
    public function msfForVoucher($data)
    {
        $attributes = $this->getAttr($data);
        MerchantSheetHistory::create($attributes);
    }
    public function returnSheetForVoucher($data)
    {
        $attributes = $this->getAttr($data);
        ReturnSheetHistory::create($attributes);
    }
}
