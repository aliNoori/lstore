<?php
namespace App\Services\Payment;

use App\Services\Payment\Gateways\MelliGateway;
use App\Services\Payment\Gateways\ParsianGateway;
use App\Services\Payment\Gateways\MellatGateway;

class PaymentGatewayFactory
{
    public static function make($gateway): PaymentGatewayInterface
    {
        return match($gateway) {
            'parsian' => new ParsianGateway(),
            'mellat' => new MellatGateway(),
            'melli' => new MelliGateway(),
            default => throw new \Exception("Unsupported payment gateway"),
        };
    }
}
