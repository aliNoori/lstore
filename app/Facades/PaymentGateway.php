<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PaymentGateway extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'PaymentGatewayFactory'; // این همان کلید ثبت شده در کانتینر لاراول است
    }
}
