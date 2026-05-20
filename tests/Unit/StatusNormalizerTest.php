<?php

namespace Qandeez\USIMPay\Tests\Unit;

use Qandeez\USIMPay\Enums\PaymentStatus;
use Qandeez\USIMPay\USIMPayClient;
use Qandeez\USIMPay\Tests\TestCase;

class StatusNormalizerTest extends TestCase
{
    public function test_it_normalizes_paid_statuses(): void
    {
        $client = app(USIMPayClient::class);

        foreach (['success', 'successful', 'paid', 'completed', '1', true] as $status) {
            $this->assertSame(PaymentStatus::Paid, $client->normalizeStatus($status));
        }
    }

    public function test_it_normalizes_failed_pending_and_unknown_statuses(): void
    {
        $client = app(USIMPayClient::class);

        $this->assertSame(PaymentStatus::Failed, $client->normalizeStatus('failed'));
        $this->assertSame(PaymentStatus::Pending, $client->normalizeStatus('pending'));
        $this->assertSame(PaymentStatus::Unknown, $client->normalizeStatus('something-else'));
    }
}
