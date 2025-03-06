<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * ورود کاربر به سیستم
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            // یافتن کاربر با ایمیل وارد شده
            $user = User::where('email', $request->email)->first();

            // بررسی درستی اطلاعات کاربر
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'اطلاعات وارد شده اشتباه است'], 401);
            }

            // ایجاد توکن برای کاربر
            $token = $user->createToken('authToken')->plainTextToken;

            // ارسال پاسخ موفقیت‌آمیز به همراه توکن
            return response()->json([
                'token' => $token ?? null,
            ], 201);
        } catch (\Exception $e) {
            // مدیریت خطاها
            return response()->json([
                'message' => 'مشکلی در فرآیند ورود به وجود آمده است',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * خروج کاربر از سیستم
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // چک کردن اینکه آیا کاربر احراز هویت شده است
            if ($request->user()) {
                // حذف توکن فعلی
                $request->user()->currentAccessToken()->delete();

                // ارسال پاسخ موفقیت‌آمیز
                return response()->json([
                    'message' => 'خروج از سیستم با موفقیت انجام شد'
                ], 200);
            }

            // اگر کاربری احراز هویت نشده باشد
            return response()->json([
                'message' => 'هیچ کاربر احراز هویت شده‌ای یافت نشد'
            ], 401);
        } catch (\Exception $e) {
            // مدیریت خطاها
            return response()->json([
                'message' => 'مشکلی در فرآیند خروج به وجود آمده است',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
