<?php

namespace Qandeez\USIMPay\Enums;

enum PaymentStatus: string
{
    case Paid = 'paid';
    case Failed = 'failed';
    case Pending = 'pending';
    case Unknown = 'unknown';

    public static function normalize(string|int|bool|null $status): self
    {
        if (is_bool($status)) {
            return $status ? self::Paid : self::Failed;
        }

        $normalized = strtolower(trim((string) $status));

        return match ($normalized) {
            'success', 'successful', 'paid', 'completed', 'complete', '1', 'true' => self::Paid,
            'failed', 'failure', 'cancelled', 'canceled', 'rejected', '0', 'false' => self::Failed,
            'pending', 'processing', 'in_progress', '09', '99' => self::Pending,
            default => self::Unknown,
        };
    }

    public function isPaid(): bool
    {
        return $this === self::Paid;
    }

    public function isFailed(): bool
    {
        return $this === self::Failed;
    }

    public function isPending(): bool
    {
        return $this === self::Pending;
    }
}
