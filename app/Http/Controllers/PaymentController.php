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
use App\Models\Wallet;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Facades\PaymentGateway;
//use App\Models\PaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class PaymentController extends Controller
{
    /**
     * آدرس بازگشت (Callback URL) برای سیستم پرداخت
     */
    //private string $CALL_BACK_URL = 'http://192.168.1.105/api/callback/payment';
    private string $CALL_BACK_URL = 'http://185.204.197.237/store/api/callback/payment';

    /**
     * پردازش درخواست پرداخت
     *
     * @param Request $request
     * @param string $order_number شماره سفارش
     * @param int $gateway_id شناسه درگاه پرداخت
     * @return JsonResponse
     * @throws Exception
     */
    public function processPayment(Request $request, string $order_number, int $gateway_id): JsonResponse
    {
        $callbackUrl = $this->CALL_BACK_URL;
        $url = null;
        // یافتن سفارش مرتبط با شماره سفارش
        $order = Order::where('order_number', $order_number)->firstOrFail();
        // مقدار پرداخت از فاکتور سفارش
        $invoice = $order->invoice;
        $amount = $invoice->total_amount;

        //Check type payment
        if ($request->paymentType === 'wallet') {
            Log::info('paymentType');

            $url = $this->processPaymentWithWallet($amount, $order_number, $gateway_id);

        }
        /*if ($request->paymentType === 'coupons') {

            $url = $this->processPaymentWithCoupons($amount, $order_number, $gateway_id);

        }*/
        if ($request->paymentType === 'online') {

            // یافتن درگاه پرداخت موردنظر
            $gateway = PaymentGateway::find($gateway_id);
            if (!$gateway) {
                return response()->json(['error' => 'Gateway not found'], 404);
            }
            // ساخت نمونه درگاه پرداخت و دریافت URL پرداخت
            $payment = PaymentGateway::make($gateway->gateway);
            $url = $payment->processPayment($amount, $order->id, $callbackUrl);
        }

        // بازگشت لینک پرداخت به فرمت JSON
        return response()->json(['url' => $url], 200, [], JSON_UNESCAPED_SLASHES);

    }

    /**
     * @throws Exception
     */
    public function processPaymentWithWallet($amount, $order_number, $id): RedirectResponse
    {
        // یافتن سفارش مرتبط با شماره سفارش
        $order = Order::where('order_number', $order_number)->firstOrFail();
        Log::info('before');
        // شروع تراکنش
        DB::beginTransaction();
        Log::info('after');
        try {
            // یافتن کیف پول کاربر بر اساس ID
            $wallet = Wallet::find($id);

            // بررسی موجودی کیف پول
            if ($wallet->balance < $amount) {
                throw new Exception('موجودی کیف پول کافی نیست.');
            }

            // ایجاد یک تراکنش جدید در کیف پول
            $transaction = new Transaction();
            $transaction->wallet_id = $wallet->id;
            $transaction->amount = $amount;
            $transaction->type = 'debit'; // نوع تراکنش (مثلاً بدهی)
            $transaction->status = 0;
            $transaction->token = 2020;
            $transaction->save();

            // کم کردن مبلغ از موجودی کیف پول
            $wallet->balance -= $amount;
            $wallet->save();
            $request = new Request([
                'SwAmount' => $amount,
            ]);
            $this->handleRewardsAndNotifications($order, $request);
            ///
            // آماده‌سازی داده‌های تراکنش
            $transaction_data = [
                'order_id' => $transaction->order_id,
                'amount' => $transaction->amount,
                'status' => $transaction->status,
                'token' => $transaction->token,
            ];


            // کاربر مرتبط با سفارش
            $user = $order->user;
            $error_message=null;
            // ساخت پارامترهای بازگشت
            $query_params = $error_message ? ['error' => $error_message] : [];
            $query_params = array_merge($query_params, $transaction_data);
            $query_params['auth_token'] = $user->createToken('UserToken')->plainTextToken;

            // ساخت لینک بازگشت به کلاینت
            //$url = 'http://192.168.1.101/your-transaction-receive';
            $url = 'http://185.204.197.237/your-transaction-receive';
            $redirect_url = $url . '?' . http_build_query($query_params);

            // تایید تراکنش
            DB::commit();

            return Redirect::to($redirect_url);
        } catch (Exception $e) {
            // لغو تراکنش در صورت بروز خطا
            DB::rollBack();

            // نمایش پیام خطا
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function processPaymentWithCoupons($amount, $order_number, $id)
    {

    }

    /**
     * پردازش پاسخ بازگشت از درگاه پرداخت
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function callbackPayment(Request $request): RedirectResponse
    {
        // بررسی وضعیت پاسخ بازگشت و وجود خطا
        $error_message = $request->CallbackError ?? null;

        if ($request->status == 0) { // تراکنش موفق

            // نوع تراکنش و روش پرداخت
            $transaction_type = 'buy';
            $payment_method = 'online';

            // ثبت تراکنش در پایگاه داده
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
                'sw_amount' => floatval(str_replace(',', '', $request->SwAmount)),
                'strace_no' => $request->STraceNo,
                'redirect_url' => $request->RedirectURL,
                'callback_error' => $request->CallbackError,
                'verify_error' => $request->VerifyError,
                'reverse_error' => $request->ReverseError,
            ]);

            // یافتن سفارش مرتبط
            $order = Order::findOrFail($request->OrderId);

            // اجرای وظایف (Jobs) مرتبط با پاداش‌ها و اطلاع‌رسانی‌ها
            $this->handleRewardsAndNotifications($order, $request);

            // آماده‌سازی داده‌های تراکنش
            $transaction_data = [
                'order_id' => $transaction->order_id,
                'amount' => $transaction->amount,
                'status' => $transaction->status,
                'token' => $transaction->token,
            ];
        }

        // کاربر مرتبط با سفارش
        $user = $order->user;

        // ساخت پارامترهای بازگشت
        $query_params = $error_message ? ['error' => $error_message] : [];
        $query_params = array_merge($query_params, $transaction_data ?? []);
        $query_params['auth_token'] = $user->createToken('UserToken')->plainTextToken;
        Log::info('url');
        // ساخت لینک بازگشت به کلاینت
        //$url = 'http://192.168.1.101/your-transaction-receive';
        $url = 'http://185.204.197.237/your-transaction-receive';
        $redirect_url = $url . '?' . http_build_query($query_params);

        return Redirect::to($redirect_url);
    }

    /**
     * پردازش پاداش‌ها و اطلاع‌رسانی‌ها برای سفارش
     *
     * @param Order $order سفارش
     * @param Request $request
     * @return void
     */
    private function handleRewardsAndNotifications(Order $order, Request $request): void
    {
        Log::info('in queue');
        // امتیاز پایه
        AddScore::dispatch($order->user_id, 10, 'امتیاز پایه', 'امتیاز پایه اولیه برای پرداخت')->onQueue('AddScore');

        // بررسی مشتری جدید و ثبت امتیاز و هدیه
        if (!Order::where('user_id', $order->user_id)->where('id', '!=', $order->id)->exists()) {
            AddScore::dispatch($order->user_id, 50, 'اولین سفارش', 'پاداش برای اولین سفارش')->onQueue('AddScore');
            AddGiftToUser::dispatch($order->id)->onQueue('AddGiftToUser');
        }

        // بررسی سفارش‌های با ارزش بالا
        $swAmount = floatval(str_replace(',', '', $request->input('SwAmount')));
        if ($swAmount > 1000) {
            AddScore::dispatch($order->user_id, 20, 'سفارش با ارزش بالا', 'پاداش برای مبلغ سفارش بیشتر از 100000')->onQueue('AddScore');
            HandleHighValueOrder::dispatch($order->id)->onQueue('HandleHighValueOrder');
        }

        // بررسی روزهای خاص
        $today = Carbon::today();
        if ($today->month == 02 && $today->day == 13) {
            AddScore::dispatch($order->user_id, 100, 'روز مخصوص', 'پاداش برای نوروز')->onQueue('AddScore');
            ApplyCoupon::dispatch($order->id)->onQueue('ApplyCoupon');
        }

        // شارژ کیف پول
        ChargeWallet::dispatch($order->user_id, $swAmount)->onQueue('ChargeWallet');

        // ارسال اطلاع‌رسانی پرداخت
        SendPaymentNotification::dispatch($order->user_id, $order->id)->onQueue('SendPaymentNotification');
    }
}
