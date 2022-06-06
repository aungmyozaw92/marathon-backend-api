<?php

namespace App\Providers;

use App\Models\Merchant;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();

        Route::bind('merchant', function ($merchant) {
            return Merchant::withTrashed()->where('id', $merchant)->firstOrFail();
        });
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapMobileRoutes();

        $this->mapWebRoutes();

        $this->mapApiMasterRoutes();

        $this->mapApiMerchantDashboardRoutes();

        $this->mapApiThirdPartyRoutes();

        $this->mapApiSupermerchantRoutes();
        //delivery web api
        $this->mapApiDeliveryRoutes();
        // Customer web api
        $this->mapApiCustomerRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    protected function mapApiMerchantDashboardRoutes()
    {
        Route::prefix('api/v1/merchant_dashboard')
            ->middleware('api')
            ->namespace($this->namespace . '\Web\Api\v1\MerchantDashboard')
            ->group(base_path('routes/merchant/merchant_dashboard.php'));
    }

    protected function mapApiThirdPartyRoutes()
    {
        Route::prefix('thirdparty/api/v1')
            ->middleware('api')
            ->namespace($this->namespace . '\Web\Api\v1\ThirdParty')
            ->group(base_path('routes/merchant/thirdparty.php'));
    }

    protected function mapApiSupermerchantRoutes()
    {
        Route::prefix('supermerchant/api/v1')
            ->middleware('api')
            ->namespace($this->namespace . '\Web\Api\v1\SuperMerchant')
            ->group(base_path('routes/merchant/supermerchant.php'));
    }

    protected function mapApiDeliveryRoutes()
    {
        Route::prefix('api/v1/delivery')
            ->middleware('api')
            ->namespace($this->namespace . '\Web\Api\v1\Delivery')
            ->group(base_path('routes/delivery/delivery.php'));
    }

    protected function mapApiCustomerRoutes()
    {
        Route::prefix('api/v1/customer')
            ->middleware('api')
            ->namespace($this->namespace . '\Web\Api\v1\Customer')
            ->group(base_path('routes/customer/customer.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapMobileRoutes()
    {
        Route::prefix('mobile/api/v1/merchant')
            ->middleware('api')
            ->namespace($this->namespace . '\Mobile\Api\v1\Merchant')
            ->group(base_path('routes/mobile/merchant.php'));

        Route::prefix('mobile/api/v1/delivery')
            ->middleware('api')
            ->namespace($this->namespace . '\Mobile\Api\v1\Delivery')
            ->group(base_path('routes/mobile/delivery.php'));

        Route::prefix('mobile/api/v1/operation')
            ->middleware('api')
            ->namespace($this->namespace . '\Mobile\Api\v1\Operation')
            ->group(base_path('routes/mobile/operation.php'));

        Route::prefix('mobile/api/v1/calculator')
            ->middleware('api', 'calculator.token')
            ->namespace($this->namespace . '\Mobile\Api\v1\Calculator')
            ->group(base_path('routes/mobile/calculator.php'));

        Route::prefix('mobile/api/v1/agent')
            ->middleware('api')
            ->namespace($this->namespace . '\Mobile\Api\v1\Agent')
            ->group(base_path('routes/mobile/agent.php'));

        Route::prefix('mobile/api/v2/merchant')
            ->middleware('api')
            ->namespace($this->namespace . '\Mobile\Api\v2\Merchant')
            ->group(base_path('routes/mobile/v2/merchant.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiMasterRoutes()
    {
        Route::prefix('api/v1')
            ->middleware('api', 'jwt.verify')
            ->namespace($this->namespace . '\Web\Api\v1')
            ->group(base_path('routes/master/master.php'));
    }
}
