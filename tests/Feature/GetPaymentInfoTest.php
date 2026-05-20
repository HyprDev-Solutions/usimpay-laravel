<?php

namespace Qandeez\USIMPay\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Qandeez\USIMPay\Enums\PaymentStatus;
use Qandeez\USIMPay\USIMPayClient;
use Qandeez\USIMPay\Tests\TestCase;

class GetPaymentInfoTest extends TestCase
{
    public function test_it_gets_payment_info(): void
    {
        Http::fake([
            'https://api.usimpay.test/api/payment/BILL123' => Http::response([
                'success' => true,
                'data' => [
                    'bill_id' => 'BILL123',
                    'merchant_id' => 'MERCHANT',
                    'payment_ref' => 'INV-001',
                    'amount' => 150.00,
                    'status' => 'completed',
                    'customer_name' => 'Customer Name',
                    'customer_email' => 'customer@example.com',
                    'fpx_txn_id' => 'FPX123',
                    'fpx_debit_auth_code' => '00',
                    'paid_at' => '2026-01-22T10:30:30.000Z',
                    'created_at' => '2026-01-22T10:00:00.000Z',
                ],
            ], 200),
        ]);

        $response = app(USIMPayClient::class)->getPaymentInfo('BILL123');

        $this->assertTrue($response->success);
        $this->assertSame('BILL123', $response->billId);
        $this->assertSame('INV-001', $response->paymentRef);
        $this->assertSame(PaymentStatus::Paid, $response->normalizedStatus);
        $this->assertTrue($response->isPaid());
    }
}
