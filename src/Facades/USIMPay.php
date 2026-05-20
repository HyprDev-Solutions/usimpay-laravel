<?php

namespace Qandeez\USIMPay\Facades;

use Illuminate\Support\Facades\Facade;
use Qandeez\USIMPay\Data\PaymentInfoResponse;
use Qandeez\USIMPay\Data\PaymentRequest;
use Qandeez\USIMPay\Data\PaymentResponse;
use Qandeez\USIMPay\Data\PaymentStatusResponse;
use Qandeez\USIMPay\Enums\PaymentStatus;

/**
 * @method static PaymentResponse initializePayment(array|PaymentRequest $request)
 * @method static PaymentResponse createPayment(array|PaymentRequest $request)
 * @method static PaymentStatusResponse verifyPaymentStatus(string $billId)
 * @method static PaymentInfoResponse getPaymentInfo(string $billId)
 * @method static string generateChecksum(array|PaymentRequest $request)
 * @method static bool isConfigured()
 * @method static PaymentStatus normalizeStatus(string|int|bool|null $status)
 * @method static bool verifyCallbackSignature(string $rawBody, ?string $signature)
 *
 * @see \Qandeez\USIMPay\USIMPayClient
 */
class USIMPay extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'usimpay';
    }
}
