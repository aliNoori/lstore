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
     * Display a listing of the resource.
     */
    //TODO:test git and deploy to server


    public function list(): JsonResponse
    {
        //
        $paymentMethods=PaymentMethod::all();
        return response()->json([
            'paymentMethods'=>PaymentMethodResource::collection($paymentMethods)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(PaymentMethodRequest $request): PaymentMethodResource
    {
        //
        //
        // دریافت داده‌های معتبر
        $validatedData = $request->all();

        $paymentMethod=PaymentMethod::create($validatedData);

        $paymentMethod->addimage($request,$paymentMethod);

        return new PaymentMethodResource($paymentMethod);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return PaymentMethodResource
     */
    public function show($id): PaymentMethodResource
    {
        //
        $shippingMethod=PaymentMethod::find($id);

        //$this->authorize('view', $category);
        return new PaymentMethodResource($shippingMethod);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  PaymentMethodRequest  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(PaymentMethodRequest $request, $id): JsonResponse
    {
        //
        //
        $paymentMethod=PaymentMethod::find($id);

        // این کد متد 'update' را در UserProduct فراخوانی می‌کند
        //$this->authorize('update', $category);

        // دریافت داده‌های معتبر
        $validatedData = $request->all();

        $paymentMethod->update($validatedData);

        $paymentMethod->updatedImageIfExist($request,$paymentMethod);

        // دوباره بارگیری کردن مدل از دیتابیس برای به‌روزرسانی اطلاعات
        $paymentMethod->refresh();


        return response()->json([
            'paymentMethod'=>new PaymentMethodResource($paymentMethod),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(Request $request, int $id): JsonResponse
    {

        //
        $paymentMethod=PaymentMethod::find($id);
        //$this->authorize('delete', $category);

        $paymentMethod->deletedImageIfExist($request,$paymentMethod);

        $paymentMethod->delete();

        return response()->json([
            'message'=>$paymentMethod->name.'deleted',
        ]);
    }
}
