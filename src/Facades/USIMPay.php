<?php

namespace USIMPay\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use USIMPay\Laravel\Data\PaymentInfoResponse;
use USIMPay\Laravel\Data\PaymentRequest;
use USIMPay\Laravel\Data\PaymentResponse;
use USIMPay\Laravel\Data\PaymentStatusResponse;
use USIMPay\Laravel\Enums\PaymentStatus;

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
 * @see \USIMPay\Laravel\USIMPayClient
 */
class USIMPay extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'usimpay';
    }
}
