<?php

namespace App\Http\Controllers;

use App\Jobs\AddGiftToUser;
use App\Jobs\AddScore;
use App\Jobs\ApplyCoupon;
use App\Jobs\ChargeWallet;
use App\Jobs\HandleHighValueOrder;
use App\Jobs\SendPaymentNotification;
use App\Models\Order;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Payment\PaymentGatewayInterface;
use App\Facades\PaymentGateway;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    //private string $CALL_BACK_URL='http://127.0.0.1:8000/api/callback/payment/';
    //private string $CALL_BACK_URL='http://192.168.1.100:8000/api/callback/payment/';
    private string $CALL_BACK_URL='http://192.168.1.105/api/callback/payment';

    public function processPayment($order_number,$gateway_id): \Illuminate\Http\JsonResponse
    {
        $callbackUrl=$this->CALL_BACK_URL;

        $gateway=\App\Models\PaymentGateway::find($gateway_id);

        $order=Order::where('order_number',$order_number)->first();

        $invoice=$order->invoice;

        $amount=$invoice->total_amount;

        $payment = PaymentGateway::make($gateway->gateway); // دسترسی به متد make به‌صورت استاتیک

        $url= $payment->processPayment($amount,$order->id, $callbackUrl);


        // خروجی مورد نظر در قالب JSON با فرمت صحیح
        return response()->json([
            'url' => $url
        ], 200, [], JSON_UNESCAPED_SLASHES); // پارامتر JSON_UNESCAPED_SLASHES برای جلوگیری از اسکیپ شدن اسلش‌ها
    }
    public function callbackPayment(Request $request)
    {
        // متغیر `error_message` در صورت وجود
        $error_message = $request->CallbackError ?? null;

        if ($request->status == 0) {
            $transaction_type = 'buy'; // transfer, buy, ...
            $payment_method = 'online';

            $transaction = Transaction::create([
                'order_id' => $request->OrderId,
                'transaction_type' => $transaction_type,
                'payment_method' => $payment_method,
                'amount' => floatval(str_replace(',', '', $request->Amount)),
                'status' => $request->status,
                'token' => $request->Token,
                'card_number_hash' => $request->HashCardNumber,
                'rrn' => $request->RRn,
                'terminal_no' => $request->TerminalNo,
                'tsp_token' => $request->TspToken,
                'sw_amount' =>floatval(str_replace(',', '', $request->SwAmount)),
                'strace_no' => $request->STraceNo,
                'redirect_url' => $request->RedirectURL,
                'callback_error' => $request->CallbackError,
                'verify_error' => $request->VerifyError,
                'reverse_error' => $request->ReverseError,
            ]);
            //////////
            $order = Order::findOrFail($request->OrderId);


            // امتیاز پایه
            AddScore::dispatch($order->user_id, 10, 'Base Score', 'Initial base score for the payment')->onQueue('AddScore');

            // بررسی مشتری جدید
            if (!Order::where('user_id', $order->user_id)->where('id', '!=', $order->id)->exists()) {
                AddScore::dispatch($order->user_id, 50, 'First Order', 'Bonus for the first order')->onQueue('AddScore');
                AddGiftToUser::dispatch($order->id)->onQueue('AddGiftToUser');
            }

            // بررسی سفارش‌های با ارزش بالا
            $swAmount = floatval(str_replace(',', '', $request->input('SwAmount')));
            if ($swAmount > 1000) {
                AddScore::dispatch($order->user_id, 20, 'High Value Order', 'Bonus for order amount greater than 1000')->onQueue('AddScore');
                HandleHighValueOrder::dispatch($order->id)->onQueue('HandleHighValueOrder');
            }

            // بررسی روزهای خاص
            $today = Carbon::today();
            if ($today->month == 11 && $today->day == 1) {

                AddScore::dispatch($order->user_id, 100, 'Special Day', 'Bonus for Christmas')->onQueue('AddScore');
                ApplyCoupon::dispatch($order->id)->onQueue('ApplyCoupon');
            }

            // شارژ کیف پول
            ChargeWallet::dispatch($order->user_id, $swAmount)->onQueue('ChargeWallet');

            // ارسال اطلاع‌رسانی پرداخت
            SendPaymentNotification::dispatch($order->user_id, $order->id)->onQueue('SendPaymentNotification');
            ///
            ///


            // داده‌های تراکنش
            $transaction_data = [
                'order_id' => $transaction->order_id,
                'amount' => $transaction->amount,
                'status' => $transaction->status,
                'token' => $transaction->token,
            ];
        }
        $user=$order->user;

        // ساخت URL و افزودن پارامترهای خطا و تراکنش
        $query_params = $error_message ? ['error' => $error_message] : [];
        $query_params = array_merge($query_params, $transaction_data ?? []);
        $query_params['auth_token'] = $user->createToken('UserToken')->plainTextToken;

        //$url = 'http://localhost:3000/your-transaction-receive';
        $url = 'http://192.168.1.100/your-transaction-receive';
        $redirect_url = $url . '?' . http_build_query($query_params);

        return Redirect::to($redirect_url);
    }
}
