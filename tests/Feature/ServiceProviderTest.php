<?php

namespace USIMPay\Laravel\Tests\Feature;

use USIMPay\Laravel\USIMPayClient;
use USIMPay\Laravel\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function test_it_resolves_client_from_container(): void
    {
        $this->assertInstanceOf(USIMPayClient::class, app(USIMPayClient::class));
        $this->assertSame(app(USIMPayClient::class), app('usimpay'));
    }
}
