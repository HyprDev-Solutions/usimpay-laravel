<?php

namespace Qandeez\USIMPay\Data;

use Qandeez\USIMPay\Enums\PaymentStatus;

class PaymentStatusResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $status = null,
        public readonly PaymentStatus $normalizedStatus = PaymentStatus::Unknown,
        public readonly ?string $billId = null,
        public readonly ?string $paymentRef = null,
        public readonly ?float $amount = null,
        public readonly ?string $fpxTxnId = null,
        public readonly ?string $paidAt = null,
        public readonly array $data = [],
        public readonly ?string $message = null,
        public readonly ?int $statusCode = null,
    ) {}

    public static function fromPaymentData(array $data, ?int $statusCode = null): self
    {
        $status = isset($data['status']) ? (string) $data['status'] : null;

        return new self(
            success: true,
            status: $status,
            normalizedStatus: PaymentStatus::normalize($status),
            billId: $data['bill_id'] ?? null,
            paymentRef: $data['payment_ref'] ?? null,
            amount: isset($data['amount']) ? (float) $data['amount'] : null,
            fpxTxnId: $data['fpx_txn_id'] ?? null,
            paidAt: $data['paid_at'] ?? null,
            data: $data,
            statusCode: $statusCode,
        );
    }

    public static function failure(?string $message = null, array $data = [], ?int $statusCode = null): self
    {
        return new self(
            success: false,
            data: $data,
            message: $message ?? 'Failed to verify payment status',
            statusCode: $statusCode,
        );
    }

    public function isPaid(): bool
    {
        return $this->normalizedStatus->isPaid();
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'status' => $this->status,
            'normalized_status' => $this->normalizedStatus->value,
            'bill_id' => $this->billId,
            'payment_ref' => $this->paymentRef,
            'amount' => $this->amount,
            'fpx_txn_id' => $this->fpxTxnId,
            'paid_at' => $this->paidAt,
            'data' => $this->data,
            'message' => $this->message,
            'status_code' => $this->statusCode,
        ];
    }
}
