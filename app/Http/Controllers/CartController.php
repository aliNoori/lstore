<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * اضافه کردن محصول به سبد خرید
     *
     * @param Request $request
     * @param int $id شناسه محصول
     * @return JsonResponse
     */
    public function addToCart(Request $request, int $id): JsonResponse
    {
        try {
            // دریافت کاربر لاگین شده
            $user = $this->getUser($request);

            // بررسی وجود سبد خرید برای کاربر
            $cart = $user->cart()->firstOrCreate(['user_id' => $user->id]);

            // پیدا کردن محصول بر اساس شناسه
            $product = Product::find($id);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'محصول مورد نظر یافت نشد'
                ], 404);
            }

            // بررسی وجود آیتم مشابه در سبد خرید
            $cartItem = $cart->cartItems()->where('product_id', $product->id)->first();

            if ($cartItem) {
                // بررسی موجودی محصول
                if ($cartItem->quantity + 1 > $product->stock) {
                    return response()->json([
                        'success' => false,
                        'message' => 'تعداد درخواستی بیشتر از موجودی محصول است'
                    ], 400);
                }
                // افزایش تعداد محصول
                $cartItem->quantity += 1;
            } else {
                // بررسی موجودی محصول
                if (1 > $product->stock) {
                    return response()->json([
                        'success' => false,
                        'message' => 'تعداد درخواستی بیشتر از موجودی محصول است'
                    ], 400);
                }
                // اضافه کردن محصول جدید
                $cartItem = new CartItem([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => 1
                ]);
            }
            $cartItem->save();

            // به‌روز رسانی تعداد کل سبد
            $cart->quantity += 1;
            $cart->save();

            return response()->json([
                'cart' => new CartResource($cart)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطایی در افزودن محصول به سبد خرید رخ داده است',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * نمایش آیتم‌های سبد خرید
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function itemsShow(Request $request): JsonResponse
    {
        try {
            // دریافت کاربر لاگین شده
            $user = $this->getUser($request);

            // بررسی وجود سبد خرید
            $cart = $user->cart()->first();
            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'سبد خرید یافت نشد'
                ], 404);
            }

            return response()->json([
                'cart' => new CartResource($cart)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطایی در نمایش سبد خرید رخ داده است',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف محصول از سبد خرید
     *
     * @param Request $request
     * @param int $id شناسه محصول
     * @return JsonResponse
     */
    public function removeFromCart(Request $request, int $id): JsonResponse
    {
        try {
            // دریافت کاربر لاگین شده
            $user = $this->getUser($request);

            // بررسی وجود سبد خرید
            $cart = $user->cart()->first();
            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'سبد خرید یافت نشد'
                ], 404);
            }

            // پیدا کردن محصول
            $product = Product::find($id);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'محصول مورد نظر یافت نشد'
                ], 404);
            }

            // بررسی وجود محصول در سبد
            $cartItem = $cart->cartItems()->where('product_id', $product->id)->first();
            if ($cartItem) {
                // کاهش تعداد محصول
                $cartItem->quantity -= 1;

                if ($cartItem->quantity <= 0) {
                    // حذف آیتم اگر تعداد صفر یا کمتر شد
                    $cartItem->delete();
                } else {
                    $cartItem->save();
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'محصول در سبد یافت نشد'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'محصول با موفقیت حذف شد',
                'cart' => new CartResource($cart->load('cartItems.product'))
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطایی در حذف محصول از سبد خرید رخ داده است',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function productInfo(Request $request, $product_id): JsonResponse
    {
        // دریافت کاربر لاگین شده
        $user = $this->getUser($request);

        // بررسی وجود سبد خرید
        $cart = $user->cart()->first();
        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'سبد خرید یافت نشد'
            ], 404);
        }

        // بررسی وجود محصول در سبد خرید
        $cartItem = $cart->cartItems()->where('product_id', $product_id)->first();
        if (!$cartItem) {
            return response()->json([
                'success' => true,
                'product_id' => $product_id,
                'quantity' => 0
            ], 200);
        }

        // تعداد محصول در سبد خرید
        $quantity = $cartItem->quantity;

        return response()->json([
            'success' => true,
            'product_id' => $product_id,
            'quantity' => $quantity
        ], 200);
    }

    /**
     * دریافت کاربر لاگین شده
     *
     * @param Request $request
     * @return \App\Models\User
     */
    protected function getUser(Request $request): \App\Models\User
    {
        return $request->user();
    }
}
