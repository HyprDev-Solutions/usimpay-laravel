<?php

namespace Qandeez\USIMPay\Data;

use InvalidArgumentException;

class PaymentRequest
{
    public function __construct(
        public readonly string $customerName,
        public readonly string $customerEmail,
        public readonly string $paymentRef,
        public readonly float $amount,
        public readonly string $paymentDescription,
        public readonly ?string $customerPhone = null,
        public readonly ?string $directUrl = null,
        public readonly ?string $callbackUrl = null,
        public readonly ?string $collectionId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        foreach (['customer_name', 'customer_email', 'payment_ref', 'amount', 'payment_description'] as $field) {
            if (! array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
                throw new InvalidArgumentException("Missing required USIMPay payment field [{$field}].");
            }
        }

        return new self(
            customerName: (string) $data['customer_name'],
            customerEmail: (string) $data['customer_email'],
            paymentRef: (string) $data['payment_ref'],
            amount: (float) $data['amount'],
            paymentDescription: (string) $data['payment_description'],
            customerPhone: isset($data['customer_phone']) ? (string) $data['customer_phone'] : null,
            directUrl: isset($data['direct_url']) ? (string) $data['direct_url'] : null,
            callbackUrl: isset($data['callback_url']) ? (string) $data['callback_url'] : null,
            collectionId: isset($data['collection_id']) ? (string) $data['collection_id'] : null,
        );
    }

    public function checksumFields(): array
    {
        $fields = [
            'amount' => number_format($this->amount, 2, '.', ''),
            'customer_email' => $this->customerEmail,
            'customer_name' => $this->customerName,
            'payment_description' => $this->paymentDescription,
            'payment_ref' => $this->paymentRef,
        ];

        if ($this->callbackUrl) {
            $fields['callback_url'] = $this->callbackUrl;
        }

        if ($this->customerPhone) {
            $fields['customer_phone'] = $this->customerPhone;
        }

        if ($this->directUrl) {
            $fields['direct_url'] = $this->directUrl;
        }

        ksort($fields);

        return $fields;
    }

    public function toPayload(string $checksum, int $descriptionMaxLength): array
    {
        $description = $descriptionMaxLength > 0
            ? mb_substr($this->paymentDescription, 0, $descriptionMaxLength)
            : $this->paymentDescription;

        return [
            'customer' => [
                'customer_name' => $this->customerName,
                'customer_email' => $this->customerEmail,
                'customer_phone' => $this->customerPhone,
            ],
            'payment' => [
                'payment_ref' => $this->paymentRef,
                'amount' => $this->amount,
                'payment_description' => $description,
                'collection_id' => $this->collectionId,
                'payment_checksum' => $checksum,
                'direct_url' => $this->directUrl,
                'callback_url' => $this->callbackUrl,
            ],
        ];
    }

    public function toArray(): array
    {
        return [
            'customer_name' => $this->customerName,
            'customer_email' => $this->customerEmail,
            'customer_phone' => $this->customerPhone,
            'payment_ref' => $this->paymentRef,
            'amount' => $this->amount,
            'payment_description' => $this->paymentDescription,
            'direct_url' => $this->directUrl,
            'callback_url' => $this->callbackUrl,
            'collection_id' => $this->collectionId,
        ];
    }
}
