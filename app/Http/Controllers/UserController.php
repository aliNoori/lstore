<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\AddressResource;
use App\Http\Resources\CouponResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ScoreResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\WalletResource;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a user profile.
     *
     * @return UserResource
     */
    public function profile(Request $request)
    {
        $user=$request->user();
        $this->authorize('view', $user);
        return new UserResource($user);
        // نمایش پروفایل کاربر فعلی
    }
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function usersList(): AnonymousResourceCollection
    {
        //
        //TODO:register role admin
        $users=User::all();
        // بازگشت مجموعه‌ای از منابع
        return UserResource::collection($users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function create(UserRequest $request): JsonResponse
    {
        //
        // دریافت داده‌های معتبر
        $validatedData = $request->all();

        // هش کردن رمز عبور برای کاربر
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user=User::create($validatedData);

        $user->addImage($request,$user);

        ///token created for user
        $token = $user->createToken('authToken', ['read', 'write'])->plainTextToken;

        return response()->json([
            'user'=>new UserResource($user),
            'token'=>$token,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        //
        $user=User::find($id);
        $this->authorize('view', $user);
        return response()->json([
            'message'=>new UserResource($user)
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(UserRequest $request, $id)
    {
        //
        $user=User::find($id);

        // این کد متد 'update' را در UserPolicy فراخوانی می‌کند
        $this->authorize('update', $user);

        // دریافت داده‌های معتبر
        $validatedData = $request->all();

        // هش کردن رمز عبور برای کاربر
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }
        $user->update($validatedData);
        $user->updatedImageIfExist($request,$user);

        // دوباره بارگیری کردن مدل از دیتابیس برای به‌روزرسانی اطلاعات
        $user->refresh();

        return response()->json([
            'user'=>new UserResource($user),
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function delete(Request $request,$id)
    {
        //
        $user=User::find($id);
        $this->authorize('delete', $user);
        $user->delete();
        $user->deletedImageIfExist($request,$user);
        return response()->json([
            'message'=>$user->name.'deleted',
        ]);
    }
    public function addAddress(AddressRequest $request): JsonResponse
    {
        // پیدا کردن کاربر
        $user = $request->user();

        //
        // دریافت داده‌های معتبر
        $validatedData = $request->all();

        // ایجاد آدرس جدید برای کاربر
        $address = $user->addresses()->create($validatedData);

        // بازگشت موفقیت‌آمیز
        return response()->json([
            'success' => true,
            'message' => 'Address added successfully',
            'address' => new AddressResource($address)
        ], 201);
    }
    public function editAddress(AddressRequest $request, $addressId): JsonResponse
    {
        // پیدا کردن کاربر
        $user = $request->user();

        // پیدا کردن آدرس کاربر که باید ویرایش شود
        $address = $user->addresses()->find($addressId);

        // بررسی وجود آدرس
        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found',
            ], 404);
        }

        // دریافت داده‌های معتبر
        $validatedData = $request->validated(); // استفاده از داده‌های معتبر شده

        // به‌روزرسانی آدرس با داده‌های جدید
        $address->update($validatedData);

        // بازگشت موفقیت‌آمیز
        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'address' => new AddressResource($address)
        ], 200);  // از کد 200 برای به‌روزرسانی موفقیت‌آمیز استفاده می‌کنیم
    }

    public function addresses(Request $request): JsonResponse
    {
        // پیدا کردن کاربر
        $user = $request->user();

        // دریافت همه آدرس‌های کاربر
        $addresses = $user->addresses()->get();

        // بازگشت موفقیت‌آمیز با لیست آدرس‌ها
        //return AddressResource::collection($addresses);
        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'addresses' => AddressResource::collection($addresses)
        ], 200);  // از کد 200 برای به‌روزرسانی موفقیت‌آمیز استفاده می‌کنیم
    }
    public function myWallet(Request $request): JsonResponse
    {
        // پیدا کردن کاربر
        $user = $request->user();

        // دریافت همه آدرس‌های کاربر
        $wallet = $user->wallet()->first();

        // بازگشت موفقیت‌آمیز با لیست آدرس‌ها
        //return AddressResource::collection($addresses);
        return response()->json([
            'success' => true,
            //'message' => 'Address updated successfully',
            'wallet' => new WalletResource($wallet)
        ], 200);  // از کد 200 برای به‌روزرسانی موفقیت‌آمیز استفاده می‌کنیم
    }
    public function myScores(Request $request): JsonResponse
    {
        // پیدا کردن کاربر
        $user = $request->user();

        // دریافت همه آدرس‌های کاربر
        $scores = $user->scores()->get();

        // بازگشت موفقیت‌آمیز با لیست آدرس‌ها
        //return AddressResource::collection($addresses);
        return response()->json([
            'success' => true,
            //'message' => 'Address updated successfully',
            'scores' => ScoreResource::collection($scores)
        ], 200);  // از کد 200 برای به‌روزرسانی موفقیت‌آمیز استفاده می‌کنیم
    }
    public function myCoupons(Request $request): JsonResponse
    {
        // پیدا کردن کاربر
        $user = $request->user();

        // دریافت همه آدرس‌های کاربر
        $coupons = $user->coupons()->get();

        // بازگشت موفقیت‌آمیز با لیست آدرس‌ها
        //return AddressResource::collection($addresses);
        return response()->json([
            'success' => true,
            //'message' => 'Address updated successfully',
            'coupons' => CouponResource::collection($coupons)
        ], 200);  // از کد 200 برای به‌روزرسانی موفقیت‌آمیز استفاده می‌کنیم
    }
    public function myOrders(Request $request): JsonResponse
    {
        // پیدا کردن کاربر
        $user = $request->user();

        // دریافت همه آدرس‌های کاربر
        $orders = $user->orders()->get();

        // بازگشت موفقیت‌آمیز با لیست آدرس‌ها
        //return AddressResource::collection($addresses);
        return response()->json([
            'success' => true,
            //'message' => 'Address updated successfully',
            'orders' => OrderResource::collection($orders)
        ], 200);  // از کد 200 برای به‌روزرسانی موفقیت‌آمیز استفاده می‌کنیم
    }
}
