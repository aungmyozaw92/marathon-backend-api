<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'voucherInSheet' => [

            'App\Events\LogHistoryEvent@voucherInSheet',

        ],
        'voucherInPickup' => [

            'App\Events\LogHistoryEvent@voucherInPickup',

        ],
        'deliSheetForVoucher' => [

            'App\Events\LogHistoryEvent@deliSheetForVoucher',

        ],
        'waybillForVoucher' => [

            'App\Events\LogHistoryEvent@waybillForVoucher',

        ],
        'msfForVoucher' => [

            'App\Events\LogHistoryEvent@msfForVoucher',

        ],
        'returnSheetForVoucher' => [

            'App\Events\LogHistoryEvent@returnSheetForVoucher',

        ],
        'pickupLogVoucher' => [

            'App\Events\LogHistoryEvent@pickupLogVoucher',

        ]

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
