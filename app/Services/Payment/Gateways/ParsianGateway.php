<?php
// app/Services/Payment/Gateways/ParsianGateway.php
namespace App\Services\Payment\Gateways;

use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use SoapClient;
use Exception;

class ParsianGateway implements PaymentGatewayInterface
{
    private string $terminalId;
    private string $wsdl;
    private string $confirm_service_client;
    private string $paymentGateway;

    public function __construct()
    {
        $this->terminalId = 'XGZ7hN6IcDieZ3bwXFAX';
        $this->wsdl = 'https://sandbox.banktest.ir/parsian/pec.shaparak.ir/NewIPGServices/Sale/SaleService.asmx?wsdl';
        $this->paymentGateway = 'https://sandbox.banktest.ir/parsian/pec.shaparak.ir/NewIPG';
        $this->confirm_service_client='https://sandbox.banktest.ir/parsian/pec.shaparak.ir/NewIPGServices/Confirm/ConfirmService.asmx?wsdl';
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
            $client = new SoapClient($this->wsdl, [
                'stream_context' => stream_context_create([
                    'socket' => ['bindto' => '0.0.0.0:0'] // ����� �� ������� �� IPv4
                ]),
                'trace'        => true,
                'cache_wsdl'   => WSDL_CACHE_NONE,
                'exceptions'   => true, // Ensures exceptions are thrown
            ]);

            $result = $client->SalePaymentRequest(['requestData' => $params]);


            if ($result->SalePaymentRequestResult->Status == 0) {  // بررسی موفقیت درخواست
                $this->token = $result->SalePaymentRequestResult->Token;
                $url = "{$this->paymentGateway}/?Token={$this->token}";
                Log::info('$url',[$url]);
                return $url;
            } else {
                throw new Exception("Error Processing Payment: " . $result->SalePaymentRequestResult->Status);
            }
        } catch (Exception $e) {
            Log::error("Parsian Gateway Error: {$e->getMessage()}");
            return null;
        }
    }
    public function confirm($token): JsonResponse
    {
        try {
            $params = [
                'LoginAccount' => $this->terminalId,
                'Token' => $token,
            ];
            $client = new SoapClient($this->confirm_service_client, ['stream_context' => stream_context_create([
                'socket' => ['bindto' => '0.0.0.0:0'] // ����� �� ������� �� IPv4
            ]),'trace' => true, 'cache_wsdl' => WSDL_CACHE_NONE]);
            $result = $client->ConfirmPayment(['requestData' => $params]);

            if ($result->ConfirmPaymentResult->Status == 0) {  // بررسی موفقیت درخواست
                $RRN = $result->ConfirmPaymentResult->RRN;
                $CardNumberMasked = $result->ConfirmPaymentResult->CardNumberMasked;
                $Token = $result->ConfirmPaymentResult->Token;
                return response()->json([
                    'success' => true,
                    'RRN' => $RRN,
                    'CardNumberMasked' => $CardNumberMasked,
                    'Token' => $Token,
                ], 200);
            } else {
                throw new Exception("Error Processing Payment: " . $result->ConfirmPaymentResult->Status);
            }
        } catch (Exception $e) {
            Log::error("Parsian Gateway Error: {$e->getMessage()}");
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
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
