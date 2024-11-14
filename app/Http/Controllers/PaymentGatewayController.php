<?php

namespace App\Http\Controllers;


use App\Http\Requests\PaymentGatewayRequest;
use App\Http\Requests\PaymentMethodRequest;
use App\Http\Resources\PaymentGatewayResource;
use App\Http\Resources\PaymentMethodResource;

use App\Models\PaymentGateway;
use App\Models\PaymentMethod;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class PaymentGatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(): JsonResponse
    {
        //
        $paymentGateways=PaymentGateway::all();
        return response()->json([
            'onlinePaymentMethods'=>PaymentGatewayResource::collection($paymentGateways)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(PaymentGatewayRequest $request): PaymentGatewayResource
    {
        //
        //
        // دریافت داده‌های معتبر
        $validatedData = $request->all();

        $paymentGateway=PaymentGateway::create($validatedData);

        $paymentGateway->addimage($request,$paymentGateway);

        return new PaymentGatewayResource($paymentGateway);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return PaymentGatewayResource
     */
    public function show($id): PaymentGatewayResource
    {
        //
        $paymentGateway=PaymentGateway::find($id);

        //$this->authorize('view', $category);
        return new PaymentGatewayResource($paymentGateway);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  PaymentMethodRequest  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(PaymentGatewayRequest $request, $id): JsonResponse
    {
        //
        //
        $paymentGateway=PaymentGateway::find($id);

        // این کد متد 'update' را در UserProduct فراخوانی می‌کند
        //$this->authorize('update', $category);

        // دریافت داده‌های معتبر
        $validatedData = $request->all();

        $paymentGateway->update($validatedData);

        $paymentGateway->updatedImageIfExist($request,$paymentGateway);

        // دوباره بارگیری کردن مدل از دیتابیس برای به‌روزرسانی اطلاعات
        $paymentGateway->refresh();


        return response()->json([
            'paymentGateway'=>new PaymentGatewayResource($paymentGateway),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(Request $request, int $id): JsonResponse
    {

        //
        $paymentGateway=PaymentGateway::find($id);
        //$this->authorize('delete', $category);

        $paymentGateway->deletedImageIfExist($request,$paymentGateway);

        $paymentGateway->delete();

        return response()->json([
            'message'=>$paymentGateway->type.'deleted',
        ]);
    }
    public function manageGateway($id)
    {
        // پیدا کردن متد پرداخت بر اساس شناسه
        $paymentMethod = PaymentMethod::find($id);

        // چک کردن اینکه آیا متد پرداخت وجود دارد
        if (!$paymentMethod) {
            return response()->json([
                'success' => false,
                'message' => 'Payment method not found.'
            ], 404);
        }

        // بررسی نوع متد پرداخت
        switch ($paymentMethod->type) {
            case 'credit_card':
                // عملیات مربوط به کارت اعتباری
                $action = "Processing credit card payment";
                break;

            case 'paypal':
                // عملیات مربوط به پی‌پال
                $action = "Processing PayPal payment";
                break;

            case 'Online':
                // عملیات مربوط به پرداخت آنلاین
                $action = "Online";
                // مثال اضافه برای افزودن قابلیت خاص مانند ادغام دروازه پرداخت
                //$gateway = PaymentGateway::where('type', 'online')->first();
                //$action .= " with gateway: " . ($gateway ? $gateway->gateway : "No gateway available");
                break;

            case 'Offline':
                // عملیات مربوط به پرداخت آفلاین
                $action = "Processing Offline payment";
                break;

            case 'Wallet':
                // عملیات مربوط به کیف پول
                $action = "Processing wallet payment";
                break;

            case 'bank_transfer':
                // عملیات مربوط به انتقال بانکی
                $action = "Processing bank transfer payment";
                break;

            case 'cash_on_delivery':
                // عملیات مربوط به پرداخت در محل
                $action = "Processing cash on delivery";
                break;

            default:
                $action = "Unknown payment method";
                break;
        }

        // بازگرداندن نتیجه
        return response()->json([
            'success' => true,
            'action' => $action
        ], 200);
    }

}
