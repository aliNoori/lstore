<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    // نمایش لیست سفارشات کاربر
    public function myOrders(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $orders = $user->orders()->with('orderDetails.product')->get();

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ]);
    }

    // ایجاد سفارش جدید
    public function createOrder(Request $request, $addressId): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $request->user();
            $cart = $user->cart()->with('cartItems.product')->first();

            if (!$cart || $cart->cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'سبد خرید خالی است',
                ], 400);
            }

            // بررسی آدرس
            $address = Address::find($addressId);
            if (!$address || $address->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'آدرس معتبر نیست',
                ], 400);
            }

            // تولید شماره سفارش
            $orderNumber = 'ORD-' . Str::random(10);

            // ایجاد سفارش
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $addressId,
                'order_number' => $orderNumber,
                'status' => 'pending',
                'total_amount' => 0, // مقدار اولیه
            ]);

            $totalAmount = 0;

            foreach ($cart->cartItems as $item) {
                $itemTotal = $item->quantity * $item->product->price;
                $totalAmount += $itemTotal;

                // ذخیره جزئیات سفارش
                $order->orderDetails()->create([
                    'product_id' => $item->product->id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'total' => $itemTotal,
                ]);
            }

            // به‌روزرسانی مبلغ کل سفارش
            $order->update(['total_amount' => $totalAmount]);

            // پاک کردن آیتم‌های سبد خرید
            $cart->cartItems()->delete();

            $order->load('orderDetails.product');

            return response()->json([
                'success' => true,
                'message' => 'سفارش با موفقیت ثبت شد',
                'order' => $order,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در ثبت سفارش',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // مشاهده جزئیات سفارش
    public function showOrder($id): \Illuminate\Http\JsonResponse
    {
        $order = Order::with('orderDetails.product')->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'سفارش یافت نشد',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
    }

    // به‌روزرسانی وضعیت سفارش
    public function updateOrderStatus(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'سفارش یافت نشد',
            ], 404);
        }

        $status = $request->input('status');
        $allowedStatuses = ['pending', 'processing', 'completed', 'cancelled'];

        if (!in_array($status, $allowedStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'وضعیت نامعتبر است',
            ], 400);
        }

        $order->update(['status' => $status]);

        return response()->json([
            'success' => true,
            'message' => 'وضعیت سفارش به‌روزرسانی شد',
            'order' => $order,
        ]);
    }

    // حذف سفارش
    public function deleteOrder($id): \Illuminate\Http\JsonResponse
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'سفارش یافت نشد',
            ], 404);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'سفارش با موفقیت حذف شد',
        ]);
    }

    // افزودن روش ارسال به سفارش
    public function addShippingToOrder($shippingId, $orderNumber): \Illuminate\Http\JsonResponse
    {
        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'سفارش یافت نشد',
            ], 404);
        }

        $order->update([
            'shipping_method_id' => $shippingId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'روش ارسال با موفقیت به سفارش اضافه شد',
        ]);
    }
}
