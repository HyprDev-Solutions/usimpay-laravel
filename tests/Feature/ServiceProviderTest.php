<?php

namespace Qandeez\USIMPay\Tests\Feature;

use Qandeez\USIMPay\USIMPayClient;
use Qandeez\USIMPay\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function test_it_resolves_client_from_container(): void
    {
        $this->assertInstanceOf(USIMPayClient::class, app(USIMPayClient::class));
        $this->assertSame(app(USIMPayClient::class), app('usimpay'));
    }
}
