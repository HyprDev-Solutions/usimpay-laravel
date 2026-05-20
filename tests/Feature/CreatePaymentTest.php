<?php

namespace USIMPay\Laravel\Tests\Feature;

use Illuminate\Support\Facades\Http;
use USIMPay\Laravel\USIMPayClient;
use USIMPay\Laravel\Tests\TestCase;

class CreatePaymentTest extends TestCase
{
    public function test_it_initializes_payment(): void
    {
        Http::fake([
            'https://api.usimpay.test/api/payment/initial' => Http::response([
                'success' => true,
                'data' => [
                    'bill_id' => 'BILL123',
                    'payment_url' => 'https://usimpay.test/payment/BILL123',
                ],
            ], 200),
        ]);

        $response = app(USIMPayClient::class)->initializePayment([
            'customer_name' => 'Customer Name',
            'customer_email' => 'customer@example.com',
            'payment_ref' => 'INV-001',
            'amount' => 150,
            'payment_description' => 'Invoice Payment',
            'direct_url' => 'https://example.com/return',
            'callback_url' => 'https://example.com/callback',
        ]);

        $this->assertTrue($response->success);
        $this->assertSame('BILL123', $response->billId);
        $this->assertSame('https://usimpay.test/payment/BILL123', $response->paymentUrl);

        Http::assertSent(fn ($request) => $request->hasHeader('X-API-Key', 'usimpay_pk_test')
            && $request['customer']['customer_email'] === 'customer@example.com'
            && $request['payment']['payment_ref'] === 'INV-001'
            && isset($request['payment']['payment_checksum']));
    }

    public function test_it_returns_failure_when_initialization_fails(): void
    {
        Http::fake([
            'https://api.usimpay.test/api/payment/initial' => Http::response([
                'success' => false,
                'message' => 'Invalid checksum',
            ], 422),
        ]);

        $response = app(USIMPayClient::class)->createPayment([
            'customer_name' => 'Customer Name',
            'customer_email' => 'customer@example.com',
            'payment_ref' => 'INV-001',
            'amount' => 150,
            'payment_description' => 'Invoice Payment',
        ]);

        $this->assertFalse($response->success);
        $this->assertSame('Invalid checksum', $response->message);
        $this->assertSame(422, $response->statusCode);
    }
}
