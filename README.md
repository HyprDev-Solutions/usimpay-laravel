# USIMPay Laravel

Laravel package for USIMPay payment integration.

This package provides the generic gateway client only:

- Generate USIMPay payment checksum
- Initialize payment
- Verify payment status
- Get payment information
- Publish Laravel config
- Use via dependency injection or facade

It does not manage your application database records, routes, UI, or business workflow.

## Installation

Install with Composer:

```bash
composer require usimpay/usimpay-laravel
```

Publish the config:

```bash
php artisan vendor:publish --tag=usimpay-config
```

Add your credentials:

```env
USIMPAY_BASE_URL=https://api.usimpay.com.my
USIMPAY_API_KEY=
USIMPAY_SECRET_KEY=
USIMPAY_TIMEOUT=30
USIMPAY_DESCRIPTION_MAX_LENGTH=20
USIMPAY_VERIFY_CALLBACKS=true
```

## Initialize Payment

```php
use USIMPay\Laravel\USIMPayClient;

$response = app(USIMPayClient::class)->initializePayment([
    'customer_name' => 'Customer Name',
    'customer_email' => 'customer@example.com',
    'customer_phone' => '+60123456789',
    'payment_ref' => 'INV-2026-00001',
    'amount' => 150.00,
    'payment_description' => 'Invoice Payment',
    'direct_url' => route('payments.redirect', ['payment' => $payment->id]),
    'callback_url' => route('payments.callback'),
]);

if ($response->success) {
    $payment->update([
        'gateway_bill_id' => $response->billId,
    ]);

    return redirect()->away($response->paymentUrl);
}

return back()->withErrors([
    'payment' => $response->message,
]);
```

`createPayment()` is also available as an alias of `initializePayment()`.

## Facade Usage

```php
use USIMPay;

$response = USIMPay::createPayment($payload);
```

## Verify Payment Status

Always verify the `bill_id` with USIMPay before fulfilling an order or marking a local record as paid.

```php
use USIMPay;

$status = USIMPay::verifyPaymentStatus($billId);

if (! $status->success) {
    abort(400, $status->message ?? 'Payment verification failed');
}

if ($status->isPaid()) {
    $payment->markAsPaid($status->toArray());
}
```

## Get Payment Info

```php
$paymentInfo = USIMPay::getPaymentInfo($billId);

if ($paymentInfo->success) {
    $amount = $paymentInfo->amount;
    $paymentRef = $paymentInfo->paymentRef;
    $fpxTxnId = $paymentInfo->fpxTxnId;
}
```

## Generate Checksum

Most applications do not need to call this directly because the client generates the checksum during payment initialization.

```php
$checksum = USIMPay::generateChecksum([
    'customer_name' => 'Customer Name',
    'customer_email' => 'customer@example.com',
    'payment_ref' => 'INV-2026-00001',
    'amount' => 150.00,
    'payment_description' => 'Invoice Payment',
]);
```

## Callback Pattern

Your application owns the callback route and local persistence:

```php
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use USIMPay;

public function callback(Request $request): JsonResponse
{
    $billId = $request->input('bill_id');
    $paymentRef = $request->input('payment_ref');

    $verified = USIMPay::verifyPaymentStatus($billId);

    if (! $verified->success) {
        return response()->json(['message' => 'Payment verification failed'], 400);
    }

    $payment = Payment::where('payment_ref', $paymentRef)->firstOrFail();

    if ($verified->isPaid()) {
        $payment->markAsPaid($verified->toArray());
    } else {
        $payment->markAsFailed($verified->toArray());
    }

    return response()->json(['status' => 'success']);
}
```

Do not trust callback payloads or redirect query parameters by themselves. Verify with USIMPay using the `bill_id`.

## Callback Signature

The package includes:

```php
USIMPay::verifyCallbackSignature($request->getContent(), $request->header('X-UsimPay-Signature'));
```

Confirm the exact signature algorithm with USIMPay before enforcing this in production.

## Testing

```bash
composer test
```

The package tests use Laravel's `Http::fake()` and do not call the real USIMPay API.

## License

MIT
