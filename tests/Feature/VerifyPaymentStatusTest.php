<?php

namespace Qandeez\USIMPay\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Qandeez\USIMPay\Enums\PaymentStatus;
use Qandeez\USIMPay\USIMPayClient;
use Qandeez\USIMPay\Tests\TestCase;

class VerifyPaymentStatusTest extends TestCase
{
    public function test_it_verifies_payment_status(): void
    {
        Http::fake([
            'https://api.usimpay.test/api/payment/BILL123' => Http::response([
                'success' => true,
                'data' => [
                    'bill_id' => 'BILL123',
                    'payment_ref' => 'INV-001',
                    'amount' => 150.00,
                    'status' => 'completed',
                    'fpx_txn_id' => 'FPX123',
                    'paid_at' => '2026-01-22T10:30:30.000Z',
                ],
            ], 200),
        ]);

        $response = app(USIMPayClient::class)->verifyPaymentStatus('BILL123');

        $this->assertTrue($response->success);
        $this->assertSame('completed', $response->status);
        $this->assertSame(PaymentStatus::Paid, $response->normalizedStatus);
        $this->assertTrue($response->isPaid());
    }

    public function test_it_returns_failure_when_verification_fails(): void
    {
        Http::fake([
            'https://api.usimpay.test/api/payment/BILL404' => Http::response([
                'success' => false,
                'message' => 'Payment not found',
            ], 404),
        ]);

        $response = app(USIMPayClient::class)->verifyPaymentStatus('BILL404');

        $this->assertFalse($response->success);
        $this->assertSame('Payment not found', $response->message);
        $this->assertSame(404, $response->statusCode);
    }
}
