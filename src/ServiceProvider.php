<?php

namespace Malbrandt\Laravel\PostponeInvoke;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Malbrandt\Laravel\PostponeInvoke\Events\KernelTerminating;

class ServiceProvider extends BaseServiceProvider
{
    /** {@inheritdoc} */
    public function register()
    {
        // Register the service the package provides.
        $this->app->singleton('postponer', function () {
            return new InvokePostponer();
        });

        app()->terminating(function () {
            event(new KernelTerminating());
        });
    }

    /** {@inheritdoc} */
    public function provides()
    {
        return ['postponer'];
    }
}
