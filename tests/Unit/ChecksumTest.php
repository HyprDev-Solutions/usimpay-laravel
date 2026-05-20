<?php

namespace Qandeez\USIMPay\Tests\Unit;

use Qandeez\USIMPay\USIMPayClient;
use Qandeez\USIMPay\Tests\TestCase;

class ChecksumTest extends TestCase
{
    public function test_it_generates_checksum_with_required_fields(): void
    {
        $checksum = app(USIMPayClient::class)->generateChecksum([
            'customer_name' => 'Customer Name',
            'customer_email' => 'customer@example.com',
            'payment_ref' => 'INV-001',
            'amount' => 150,
            'payment_description' => 'Invoice Payment',
        ]);

        $expectedString = '36EDC6A3|150.00|customer@example.com|Customer Name|Invoice Payment|INV-001|B831F6A5';

        $this->assertSame(hash('sha256', $expectedString), $checksum);
    }

    public function test_it_generates_checksum_with_optional_fields_sorted_by_key(): void
    {
        $checksum = app(USIMPayClient::class)->generateChecksum([
            'customer_name' => 'Customer Name',
            'customer_email' => 'customer@example.com',
            'customer_phone' => '+60123456789',
            'payment_ref' => 'INV-002',
            'amount' => 10.5,
            'payment_description' => 'Invoice Payment',
            'direct_url' => 'https://example.com/return',
            'callback_url' => 'https://example.com/callback',
        ]);

        $expectedString = '36EDC6A3|10.50|https://example.com/callback|customer@example.com|Customer Name|+60123456789|https://example.com/return|Invoice Payment|INV-002|B831F6A5';

        $this->assertSame(hash('sha256', $expectedString), $checksum);
    }
}
