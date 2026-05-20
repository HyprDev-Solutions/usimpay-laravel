<?php

namespace USIMPay\Laravel\Data;

class PaymentResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $paymentUrl = null,
        public readonly ?string $billId = null,
        public readonly ?string $message = null,
        public readonly array $data = [],
        public readonly ?int $statusCode = null,
    ) {}

    public static function success(array $data, ?int $statusCode = null): self
    {
        return new self(
            success: true,
            paymentUrl: $data['payment_url'] ?? null,
            billId: $data['bill_id'] ?? null,
            data: $data,
            statusCode: $statusCode,
        );
    }

    public static function failure(?string $message = null, array $data = [], ?int $statusCode = null): self
    {
        return new self(
            success: false,
            message: $message ?? 'Payment initialization failed',
            data: $data,
            statusCode: $statusCode,
        );
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'payment_url' => $this->paymentUrl,
            'bill_id' => $this->billId,
            'message' => $this->message,
            'data' => $this->data,
            'status_code' => $this->statusCode,
        ];
    }
}
