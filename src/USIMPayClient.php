<?php

namespace USIMPay\Laravel;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use USIMPay\Laravel\Data\PaymentInfoResponse;
use USIMPay\Laravel\Data\PaymentRequest;
use USIMPay\Laravel\Data\PaymentResponse;
use USIMPay\Laravel\Data\PaymentStatusResponse;
use USIMPay\Laravel\Enums\PaymentStatus;
use Throwable;

class USIMPayClient
{
    public function initializePayment(array|PaymentRequest $request): PaymentResponse
    {
        try {
            $paymentRequest = $this->paymentRequest($request);
            $checksum = $this->generateChecksum($paymentRequest);
            $payload = $paymentRequest->toPayload($checksum, $this->descriptionMaxLength());

            $this->log('info', 'USIMPay: Initializing payment', [
                'payment_ref' => $paymentRequest->paymentRef,
                'amount' => $paymentRequest->amount,
                'base_url' => $this->baseUrl(),
            ]);

            $response = $this->http()
                ->post($this->url('/api/payment/initial'), $payload);

            $body = $this->json($response);

            $this->log('info', 'USIMPay: Initialize payment response', [
                'status' => $response->status(),
                'body' => $body,
            ]);

            if ($response->successful() && ($body['success'] ?? false)) {
                return PaymentResponse::success($body['data'] ?? $body, $response->status());
            }

            return PaymentResponse::failure(
                $body['message'] ?? $body['error'] ?? 'Payment initialization failed',
                $body,
                $response->status(),
            );
        } catch (Throwable $e) {
            $this->log('error', 'USIMPay: Exception during payment initialization', [
                'error' => $e->getMessage(),
            ]);

            return PaymentResponse::failure('Payment service unavailable. Please try again later.');
        }
    }

    public function createPayment(array|PaymentRequest $request): PaymentResponse
    {
        return $this->initializePayment($request);
    }

    public function verifyPaymentStatus(string $billId): PaymentStatusResponse
    {
        return $this->getPaymentInfo($billId)->toStatusResponse();
    }

    public function getPaymentInfo(string $billId): PaymentInfoResponse
    {
        try {
            $response = $this->http()
                ->get($this->url("/api/payment/{$billId}"));

            $body = $this->json($response);

            $this->log('info', 'USIMPay: Payment info response', [
                'bill_id' => $billId,
                'status' => $response->status(),
                'body' => $body,
            ]);

            if ($response->successful() && ($body['success'] ?? false)) {
                return PaymentInfoResponse::fromPaymentData($body['data'] ?? $body, $response->status());
            }

            return PaymentInfoResponse::failure(
                $body['message'] ?? $body['error'] ?? 'Failed to retrieve payment information',
                $body,
                $response->status(),
            );
        } catch (Throwable $e) {
            $this->log('error', 'USIMPay: Exception during payment information request', [
                'bill_id' => $billId,
                'error' => $e->getMessage(),
            ]);

            return PaymentInfoResponse::failure('Payment information unavailable');
        }
    }

    public function generateChecksum(array|PaymentRequest $request): string
    {
        $paymentRequest = $this->paymentRequest($request);
        $secretKey = $this->secretKey() ?? '';

        $firstHalf = substr($secretKey, 0, 8);
        $secondHalf = substr($secretKey, 8, 8);
        $fields = $paymentRequest->checksumFields();
        $checksumString = $secondHalf.'|'.implode('|', array_values($fields)).'|'.$firstHalf;

        return hash('sha256', $checksumString);
    }

    public function verifyCallbackSignature(string $rawBody, ?string $signature): bool
    {
        if (! $signature || ! $this->secretKey()) {
            return false;
        }

        $expected = hash_hmac('sha256', $rawBody, $this->secretKey());

        return hash_equals($expected, $signature);
    }

    public function normalizeStatus(string|int|bool|null $status): PaymentStatus
    {
        return PaymentStatus::normalize($status);
    }

    public function isConfigured(): bool
    {
        return filled($this->apiKey()) && filled($this->secretKey());
    }

    protected function paymentRequest(array|PaymentRequest $request): PaymentRequest
    {
        return $request instanceof PaymentRequest ? $request : PaymentRequest::fromArray($request);
    }

    protected function http(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-API-Key' => $this->apiKey(),
        ])->timeout($this->timeout());
    }

    protected function json(Response $response): array
    {
        $json = $response->json();

        return is_array($json) ? $json : [];
    }

    protected function url(string $path): string
    {
        return rtrim($this->baseUrl(), '/').'/'.ltrim($path, '/');
    }

    protected function baseUrl(): string
    {
        return (string) config('usimpay.base_url', 'https://api.usimpay.com.my');
    }

    protected function apiKey(): ?string
    {
        return config('usimpay.api_key');
    }

    protected function secretKey(): ?string
    {
        return config('usimpay.secret_key');
    }

    protected function timeout(): int
    {
        return (int) config('usimpay.timeout', 30);
    }

    protected function descriptionMaxLength(): int
    {
        return (int) config('usimpay.description_max_length', 20);
    }

    protected function log(string $level, string $message, array $context = []): void
    {
        $channel = config('usimpay.log_channel');

        if ($channel) {
            Log::channel($channel)->{$level}($message, $context);

            return;
        }

        Log::{$level}($message, $context);
    }
}
