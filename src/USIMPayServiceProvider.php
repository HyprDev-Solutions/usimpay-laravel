<?php

namespace USIMPay\Laravel;

use Illuminate\Support\ServiceProvider;

class USIMPayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/usimpay.php', 'usimpay');

        $this->app->singleton(USIMPayClient::class, fn () => new USIMPayClient);
        $this->app->alias(USIMPayClient::class, 'usimpay');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/usimpay.php' => config_path('usimpay.php'),
        ], 'usimpay-config');
    }
}
