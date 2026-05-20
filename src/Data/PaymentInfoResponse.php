<?php

namespace Qandeez\USIMPay\Data;

use Qandeez\USIMPay\Enums\PaymentStatus;

class PaymentInfoResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $billId = null,
        public readonly ?string $merchantId = null,
        public readonly ?string $paymentRef = null,
        public readonly ?float $amount = null,
        public readonly ?string $status = null,
        public readonly PaymentStatus $normalizedStatus = PaymentStatus::Unknown,
        public readonly ?string $customerName = null,
        public readonly ?string $customerEmail = null,
        public readonly ?string $fpxTxnId = null,
        public readonly ?string $fpxDebitAuthCode = null,
        public readonly ?string $paidAt = null,
        public readonly ?string $createdAt = null,
        public readonly array $data = [],
        public readonly ?string $message = null,
        public readonly ?int $statusCode = null,
    ) {}

    public static function fromPaymentData(array $data, ?int $statusCode = null): self
    {
        $status = isset($data['status']) ? (string) $data['status'] : null;

        return new self(
            success: true,
            billId: $data['bill_id'] ?? null,
            merchantId: $data['merchant_id'] ?? null,
            paymentRef: $data['payment_ref'] ?? null,
            amount: isset($data['amount']) ? (float) $data['amount'] : null,
            status: $status,
            normalizedStatus: PaymentStatus::normalize($status),
            customerName: $data['customer_name'] ?? null,
            customerEmail: $data['customer_email'] ?? null,
            fpxTxnId: $data['fpx_txn_id'] ?? null,
            fpxDebitAuthCode: $data['fpx_debit_auth_code'] ?? null,
            paidAt: $data['paid_at'] ?? null,
            createdAt: $data['created_at'] ?? null,
            data: $data,
            statusCode: $statusCode,
        );
    }

    public static function failure(?string $message = null, array $data = [], ?int $statusCode = null): self
    {
        return new self(
            success: false,
            data: $data,
            message: $message ?? 'Failed to retrieve payment information',
            statusCode: $statusCode,
        );
    }

    public function isPaid(): bool
    {
        return $this->normalizedStatus->isPaid();
    }

    public function toStatusResponse(): PaymentStatusResponse
    {
        if (! $this->success) {
            return PaymentStatusResponse::failure($this->message, $this->data, $this->statusCode);
        }

        return PaymentStatusResponse::fromPaymentData($this->data, $this->statusCode);
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'bill_id' => $this->billId,
            'merchant_id' => $this->merchantId,
            'payment_ref' => $this->paymentRef,
            'amount' => $this->amount,
            'status' => $this->status,
            'normalized_status' => $this->normalizedStatus->value,
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail,
            'fpx_txn_id' => $this->fpxTxnId,
            'fpx_debit_auth_code' => $this->fpxDebitAuthCode,
            'paid_at' => $this->paidAt,
            'created_at' => $this->createdAt,
            'data' => $this->data,
            'message' => $this->message,
            'status_code' => $this->statusCode,
        ];
    }
}
