<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentMethodRequest;
use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * لیست تمامی روش‌های پرداخت را برمی‌گرداند.
     */
    public function list(): JsonResponse
    {
        $paymentMethods = PaymentMethod::all();
        return response()->json([
            'paymentMethods' => PaymentMethodResource::collection($paymentMethods),
        ]);
    }

    /**
     * ایجاد یک روش پرداخت جدید.
     */
    public function create(PaymentMethodRequest $request): PaymentMethodResource
    {
        // دریافت داده‌های معتبر
        $validatedData = $request->validated();

        // ایجاد روش پرداخت
        $paymentMethod = PaymentMethod::create($validatedData);

        // افزودن تصویر (در صورت وجود)
        $paymentMethod->addImage($request, $paymentMethod);

        return new PaymentMethodResource($paymentMethod);
    }

    /**
     * نمایش جزئیات یک روش پرداخت خاص.
     */
    public function show(int $id): PaymentMethodResource
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        return new PaymentMethodResource($paymentMethod);
    }

    /**
     * به‌روزرسانی یک روش پرداخت.
     */
    public function update(PaymentMethodRequest $request, int $id): JsonResponse
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        // دریافت داده‌های معتبر
        $validatedData = $request->validated();

        // به‌روزرسانی روش پرداخت
        $paymentMethod->update($validatedData);

        // به‌روزرسانی تصویر (در صورت وجود)
        $paymentMethod->updatedImageIfExist($request, $paymentMethod);

        // بارگذاری مجدد مدل
        $paymentMethod->refresh();

        return response()->json([
            'paymentMethod' => new PaymentMethodResource($paymentMethod),
        ]);
    }

    /**
     * حذف یک روش پرداخت.
     */
    public function delete(int $id): JsonResponse
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        // حذف تصویر مرتبط (در صورت وجود)
        $paymentMethod->deletedImageIfExist();

        // حذف روش پرداخت
        $paymentMethod->delete();

        return response()->json([
            'message' => $paymentMethod->name . ' deleted',
        ]);
    }
}
