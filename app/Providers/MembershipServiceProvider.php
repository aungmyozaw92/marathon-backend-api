<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contacts\MembershipContract;
use App\Services\HeroMembershipService;


class MembershipServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind('App\Contracts\MembershipContract', HeroMembershipService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
