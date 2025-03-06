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
     * لیست تمام درگاه‌های پرداخت را برمی‌گرداند.
     */
    public function list(): JsonResponse
    {
        $paymentGateways = PaymentGateway::all();
        return response()->json([
            'onlinePaymentMethods' => PaymentGatewayResource::collection($paymentGateways),
        ]);
    }

    /**
     * ایجاد یک درگاه پرداخت جدید.
     */
    public function create(PaymentGatewayRequest $request): PaymentGatewayResource
    {
        // دریافت داده‌های معتبر از درخواست
        $validatedData = $request->validated();

        // ایجاد درگاه پرداخت
        $paymentGateway = PaymentGateway::create($validatedData);

        // افزودن تصویر (در صورت وجود)
        $paymentGateway->addImage($request, $paymentGateway);

        return new PaymentGatewayResource($paymentGateway);
    }

    /**
     * نمایش جزئیات یک درگاه پرداخت خاص.
     */
    public function show(int $id): PaymentGatewayResource
    {
        $paymentGateway = PaymentGateway::findOrFail($id);
        return new PaymentGatewayResource($paymentGateway);
    }

    /**
     * به‌روزرسانی یک درگاه پرداخت.
     */
    public function update(PaymentGatewayRequest $request, int $id): JsonResponse
    {
        $paymentGateway = PaymentGateway::findOrFail($id);

        // دریافت داده‌های معتبر
        $validatedData = $request->validated();

        // به‌روزرسانی اطلاعات درگاه پرداخت
        $paymentGateway->update($validatedData);

        // به‌روزرسانی تصویر در صورت وجود
        $paymentGateway->updatedImageIfExist($request, $paymentGateway);

        // بارگذاری مجدد مدل
        $paymentGateway->refresh();

        return response()->json([
            'paymentGateway' => new PaymentGatewayResource($paymentGateway),
        ]);
    }

    /**
     * حذف یک درگاه پرداخت.
     */
    public function delete(int $id): JsonResponse
    {
        $paymentGateway = PaymentGateway::findOrFail($id);

        // حذف تصویر مرتبط (در صورت وجود)
        $paymentGateway->deletedImageIfExist();

        // حذف درگاه پرداخت
        $paymentGateway->delete();

        return response()->json([
            'message' => $paymentGateway->type . ' deleted',
        ]);
    }

    /**
     * مدیریت روش پرداخت بر اساس نوع.
     */
    public function manageGateway(int $id): JsonResponse
    {
        $paymentMethod = PaymentMethod::find($id);

        if (!$paymentMethod) {
            return response()->json([
                'success' => false,
                'message' => 'Payment method not found.',
            ], 404);
        }

        // بررسی نوع متد پرداخت
        $action = match ($paymentMethod->type) {
            //'credit_card' => 'Processing credit card payment',
            //'PayPal' => 'Processing PayPal payment',
            'Online' => 'Online',//'Processing online payment',
            //'Offline' => 'Offline',//Processing offline payment',
            'Wallet' => 'Wallet',
            'OtherWays' => 'OtherWays',
            //'bank_transfer' => 'Processing bank transfer payment',
            //'cash_on_delivery' => 'Processing cash on delivery',
            default => 'Unknown payment method',
        };

        return response()->json([
            'success' => true,
            'action' => $action,
        ]);
    }
}
