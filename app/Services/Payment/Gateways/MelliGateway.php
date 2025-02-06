<?php
namespace App\Services\Payment\Gateways;

use App\Services\Payment\PaymentGatewayInterface;

class MelliGateway implements PaymentGatewayInterface
{
    public function initiatePayment($amount, $callbackUrl)
    {
        // پیاده‌سازی مربوط به پرداخت زرین‌پال
    }

    public function verifyPayment($request)
    {
        // پیاده‌سازی مربوط به تایید پرداخت زرین‌پال
    }
}
