<?php
namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    // نمایش لیست سفارشات کاربر
    public function myOrders(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $orders = $user->orders()->with('orderItems.product')->get();

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ]);
    }

    // ایجاد سفارش جدید
    public function createOrder(Request $request,$id): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $cart = $user->cart()->with('cartItems.product')->first();

        if (!$cart || $cart->cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty',
            ], 400);
        }

        // تولید شماره سفارش منحصر به فرد
        $orderNumber = 'ORD-' . Str::random(10); // تولید شماره سفارش تصادفی

        // ایجاد سفارش
        $order = Order::create([
            'user_id' => $user->id,
            'address_id'=>$id,
            'order_number'=>$orderNumber,
            'status' => 'pending', // وضعیت اولیه
            'total_amount' => 0, // محاسبه در ادامه
        ]);


        $totalAmount = 0;

        foreach ($cart->cartItems as $item) {

            $itemTotal = $item->quantity * $item->product->price; // محاسبه مبلغ کل آیتم

            $totalAmount += $itemTotal; // اضافه کردن به مبلغ کل سفارش

            // ذخیره آیتم‌های سفارش
            $order->OrderDetails()->create([
                'product_id' => $item->product->id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
                'total' => $itemTotal,
            ]);
        }

        // به‌روزرسانی مبلغ کل در سفارش
        $order->update(['total_amount' => $totalAmount]);


        // به‌روزرسانی مبلغ کل سفارش
        $order->update(['total_amount' => $totalAmount]);

        // پاک کردن آیتم‌های سبد خرید پس از ثبت سفارش
        $cart->cartItems()->delete();

        $order->load('orderDetails.product');

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'order' => $order,
        ], 201);
    }

    // مشاهده جزئیات سفارش
    public function showOrder($id): \Illuminate\Http\JsonResponse
    {
        $order = Order::with('orderItems.product')->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
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
                'message' => 'Order not found',
            ], 404);
        }

        $order->update([
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated',
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
                'message' => 'Order not found',
            ], 404);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully',
        ]);
    }
    public function addShippingToOrder($shipping_id,$order_number): \Illuminate\Http\JsonResponse
    {
        $order = Order::where('order_number',$order_number)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        $order->update([
            'shipping_method_id' => $shipping_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order add shipping method successfully',
        ]);
    }
}
