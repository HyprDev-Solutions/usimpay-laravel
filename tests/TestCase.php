<?php

namespace Qandeez\USIMPay\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Qandeez\USIMPay\USIMPayServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            USIMPayServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('usimpay.base_url', 'https://api.usimpay.test');
        $app['config']->set('usimpay.api_key', 'usimpay_pk_test');
        $app['config']->set('usimpay.secret_key', 'B831F6A536EDC6A3');
        $app['config']->set('usimpay.timeout', 30);
        $app['config']->set('usimpay.description_max_length', 20);
    }
}
