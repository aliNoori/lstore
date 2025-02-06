<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\ShippingMethod;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    /**
     * ایجاد فاکتور جدید
     *
     * @param InvoiceRequest $request
     * @param string $order_number شماره سفارش
     * @return JsonResponse
     */
    public function create(InvoiceRequest $request, string $order_number): JsonResponse
    {
        try {
            // دریافت کاربر لاگین شده
            $user = $request->user();

            // بررسی وجود سفارش بر اساس شماره سفارش
            $order = Order::where('order_number', $order_number)
                ->with('orderDetails.product')
                ->first();

            if (!$order || $order->orderDetails->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'سفارش یافت نشد یا خالی است'
                ], 404);
            }

            // تولید شماره فاکتور منحصر به فرد
            $invoiceNumber = 'INV-' . Str::random(10);
            $dueDate = Carbon::now()->addDays(30);

            // ایجاد فاکتور جدید
            $invoice = Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => $invoiceNumber,
                'due_date' => $dueDate,
                'total_amount' => 0,  // در ابتدا 0، بعداً محاسبه می‌شود
                'status' => 'pending',
                'sub_total_amount' => 0 // در ابتدا 0، بعداً محاسبه می‌شود
            ]);

            $subTotalAmount = 0;

            // اضافه کردن آیتم‌های سفارش به فاکتور
            foreach ($order->orderDetails as $orderItem) {
                $product = $orderItem->product;
                $quantity = $orderItem->quantity;
                $price = $product->price;
                $total = $quantity * $price;
                $discount = $product->discount ?? 0;
                $price_with_discount = $total * (1 - ($discount / 100));

                // ایجاد آیتم فاکتور
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $total,
                    'discount' => $discount,
                    'price_with_discount' => $price_with_discount,
                    'description' => '', // توضیحات در صورت نیاز
                ]);

                $subTotalAmount += $price_with_discount;
            }

            // اضافه کردن هزینه ارسال (در صورت وجود)
            $shipping_cost = 0;
            if ($order->shipping_method_id) {
                $shipping_method = ShippingMethod::find($order->shipping_method_id);
                if ($shipping_method) {
                    $shipping_cost = $shipping_method->cost;
                }
            }

            // محاسبه مالیات و مبلغ کل فاکتور
            $tax_rate = 10; // نرخ مالیات
            $tax = $subTotalAmount * ($tax_rate / 100);
            $total_amount = $subTotalAmount + $tax + $shipping_cost;

            // به‌روزرسانی مبلغ کل فاکتور
            $invoice->update([
                'sub_total_amount' => $subTotalAmount,
                'tax_rate' => $tax_rate,
                'tax' => $tax,
                'total_amount' => $total_amount,
                'shipping_cost' => $shipping_cost
            ]);

            // بازگشت موفقیت‌آمیز با جزئیات فاکتور
            return response()->json([
                'success' => true,
                'invoice' => new InvoiceResource($invoice)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در ایجاد فاکتور',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * نمایش جزئیات فاکتور
     *
     * @param Invoice $invoice
     * @return JsonResponse
     */
    public function show(Invoice $invoice): JsonResponse
    {
        return response()->json([
            'success' => true,
            'invoice' => new InvoiceResource($invoice)
        ], 200);
    }

    /**
     * به‌روزرسانی فاکتور
     *
     * @param Request $request
     * @param Invoice $invoice
     * @return void
     */
    public function update(Request $request, Invoice $invoice)
    {
        // کد به‌روزرسانی در صورت نیاز
    }

    /**
     * حذف فاکتور
     *
     * @param Invoice $invoice
     * @return JsonResponse
     */
    public function delete(Invoice $invoice): JsonResponse
    {
        try {
            $invoice->delete();

            return response()->json([
                'success' => true,
                'message' => 'فاکتور با موفقیت حذف شد'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در حذف فاکتور',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
