<?php

namespace App\Providers;

use Laravel\Telescope\Telescope;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
       
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        Telescope::filter(function (IncomingEntry $entry) {
            if ($this->app->isLocal() || $this->app->environment() == 'staging') {
                return true;
            }

            return $entry->isReportableException() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     *
     * @return void
     */
    protected function hideSensitiveRequestDetails()
    {
        if ($this->app->isLocal() || $this->app->environment() == 'staging') {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewTelescope', function ($user) {
            return in_array($user->email, [
                'sayargyi.sayargyi@marathonmyanmar.com',
                'azb.sayargyi@marathonmyanmar.com',
                'amz.sayargyi@marathonmyanmar.com',
                'kyn.sayargyi@marathonmyanmar.com',
                'akkm.sayargyi@marathonmyanmar.com',
                'nnb.sayargyi@marathonmyanmar.com',
                'nzt.sayargyi@marathonmyanmar.com',
                'swa.sayargyi@marathonmyanmar.com'
            ]);
        });
    }

    // protected function gate()
    // {
    //     Gate::define('viewTelescope', function ($user) {
            
    //         $ids = env('TELESCOPE_USERS', '');
    //         $ids = explode(',', $ids);
    //         return in_array($user->id, $ids);
    //     });
    // }
}
