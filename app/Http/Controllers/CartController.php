<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Add a product to the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id  Product ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToCart(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        // دریافت کاربر لاگین شده
        $user = $request->user();

        // بررسی وجود سبد خرید برای کاربر
        $cart = $user->cart()->firstOrCreate([
            'user_id' => $user->id,
        ]);

        // پیدا کردن محصول بر اساس id
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // بررسی وجود آیتم مشابه در سبد خرید
        $cartItem = $cart->cartItems()->where('product_id', $product->id)->first();

        if ($cartItem) {
            // اگر محصول قبلاً در سبد خرید باشد، تعداد آن را افزایش می‌دهیم
            $cartItem->quantity += 1;
        } else {
            // در غیر این صورت، یک آیتم جدید اضافه می‌کنیم
            $cartItem = new CartItem();
            $cartItem->cart_id = $cart->id;
            $cartItem->product_id = $product->id;
            $cartItem->quantity = 1;
        }
        $cartItem->save();

        $cart->quantity+=1;
        $cart->save();

        return response()->json(['cart'=> new CartResource($cart)],200);

       /* return response()->json([
            'cart' => $cart->load('cartItems.product') // بازگشت سبد خرید به همراه محصولات
        ],200);*/
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\JsonResponse
     */
    public function itemsShow(Request $request): \Illuminate\Http\JsonResponse
    {
        // دریافت کاربر لاگین شده
        $user = $this->getUser($request);


        // بررسی وجود سبد خرید برای کاربر
        $cart = $user->cart()->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Cart not found'
            ], 404);
        }

        return response()->json(['cart'=> new CartResource($cart)],200);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFromCart(Request $request, $id)
    {
        // دریافت کاربر لاگین شده
        $user = $request->user();

        // بررسی وجود سبد خرید برای کاربر
        $cart = $user->cart()->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Cart not found'
            ], 404);
        }

        // پیدا کردن محصول بر اساس id
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // بررسی وجود آیتم مشابه در سبد خرید
        $cartItem = $cart->cartItems()->where('product_id', $product->id)->first();

        if ($cartItem) {
            // اگر محصول قبلاً در سبد خرید باشد، تعداد آن را کاهش می‌دهیم
            $cartItem->quantity -= 1;

            if ($cartItem->quantity <= 0) {
                // اگر تعداد آیتم به صفر یا کمتر رسید، آن را از سبد حذف می‌کنیم
                $cartItem->delete();
            } else {
                // در غیر این صورت، فقط مقدار آن را به‌روز رسانی می‌کنیم
                $cartItem->save();
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart'
            ], 404);
        }

        // بازگشت سبد خرید به‌روز شده
        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart',
            'cart' => new CartResource($cart->load('cartItems.product')) // سبد به همراه آیتم‌ها و محصولات
        ]);
    }

    protected function getUser($request){

        // دریافت کاربر لاگین شده
        return $user = $request->user();
    }

}
