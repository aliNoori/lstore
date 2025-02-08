<?php
// app/Services/Payment/Gateways/ParsianGateway.php
namespace App\Services\Payment\Gateways;

use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Support\Facades\Log;
use SoapClient;
use Exception;

class ParsianGateway implements PaymentGatewayInterface
{
    private $terminalId;
    private $wsdl;
    private $paymentGateway;

    public function __construct()
    {
        $this->terminalId = 'XGZ7hN6IcDieZ3bwXFAX';
        $this->wsdl = 'https://sandbox.banktest.ir/parsian/pec.shaparak.ir/NewIPGServices/Sale/SaleService.asmx?wsdl';
        $this->paymentGateway = 'https://sandbox.banktest.ir/parsian/pec.shaparak.ir/NewIPG';
    }

    public function processPayment($amount, $orderId, $callbackUrl): ?string
    {
        $params = [
            'LoginAccount' => $this->terminalId,
            'Amount' => $amount,
            'OrderId' => $orderId,
            'CallBackUrl' => $callbackUrl,
            'AdditionalData' => 'Test data',
            'Originator' => '',
        ];

        try {
            // ایجاد کلاینت Soap و درخواست
            $client = new SoapClient($this->wsdl, ['trace' => true, 'cache_wsdl' => WSDL_CACHE_NONE]);
            $result = $client->SalePaymentRequest(['requestData' => $params]);


            if ($result->SalePaymentRequestResult->Status == 0) {  // بررسی موفقیت درخواست
                $token = $result->SalePaymentRequestResult->Token;
                $url = "{$this->paymentGateway}/?Token={$token}";
                return $url;
            } else {
                throw new Exception("Error Processing Payment: " . $result->SalePaymentRequestResult->Status);
            }
        } catch (Exception $e) {
            Log::error("Parsian Gateway Error: {$e->getMessage()}");
            return null;
        }
    }

    public function refund($transactionId)
    {
        // پیاده‌سازی منطقی برای استرداد
        Log::info("Refunding transaction {$transactionId}");
        return true;
    }

    public function getStatus($transactionId)
    {
        // پیاده‌سازی برای دریافت وضعیت تراکنش
        Log::info("Getting status of transaction {$transactionId}");
        return 'completed';
    }
}
