<?php

namespace App\Services\Payment;

interface PaymentGatewayInterface
{
    public function processPayment($amount, $orderId, $callbackUrl);
    public function refund($transactionId);
    public function getStatus($transactionId);
}
