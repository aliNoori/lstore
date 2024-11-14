<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\ShippingMethod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function create(InvoiceRequest $request, $order_number)
    {
        // دریافت کاربر لاگین شده
        $user = $request->user();

        // بررسی وجود سفارش بر اساس order_number
        $order = Order::where('order_number', $order_number)->with('orderDetails.product')->first();

        if (!$order || $order->orderDetails->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or empty'
            ], 404);
        }

        // تولید شماره فاکتور منحصر به فرد
        $invoiceNumber = 'INV-' . Str::random(10);
        $dueDate = Carbon::now()->addDays(30);

        // ایجاد فاکتور جدید
        $invoice = Invoice::create([
            'order_id'=>$order->id,
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
            $discount = $product->discount;
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

        // اضافه کردن هزینه ارسال از فیلد order

        $shipping_cost = 0;
        $shipping_method = null;
        $shipping_method_id = $order->shipping_method_id;
        if($shipping_method_id){

            $shipping_method=ShippingMethod::where('id',$shipping_method_id)->first();
        }
        if($shipping_method){

            $shipping_cost=$shipping_method->cost;

        }
        // محاسبه مالیات و مبلغ کل فاکتور
        $tax_rate = 10;
        $tax = $subTotalAmount * ($tax_rate / 100);
        ///
        $total_amount = $subTotalAmount + $tax + $shipping_cost ;

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
            'invoice' => new InvoiceResource($invoice)
        ], 201);
    }


    public function show(Invoice $invoice)
    {
        //
    }

    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    public function delete(Invoice $invoice)
    {
        //
    }
}
