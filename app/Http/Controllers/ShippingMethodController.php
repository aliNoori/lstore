<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShippingMethodRequest;
use App\Http\Resources\ShippingMethodResource;
use App\Models\ShippingMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class ShippingMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(): JsonResponse
    {
        //
        $shippingMethods=ShippingMethod::all();
        return response()->json([
            'shippingMethods'=>ShippingMethodResource::collection($shippingMethods)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ShippingMethodRequest $request): ShippingMethodResource
    {
        //
        //
        // دریافت داده‌های معتبر
        $validatedData = $request->all();

        $shippingMethod=ShippingMethod::create($validatedData);

        $shippingMethod->addimage($request,$shippingMethod);

        return new ShippingMethodResource($shippingMethod);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return ShippingMethodResource
     */
    public function show($id): ShippingMethodResource
    {
        //
        $shippingMethod=ShippingMethod::find($id);

        //$this->authorize('view', $category);
        return new ShippingMethodResource($shippingMethod);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  ShippingMethodRequest  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(ShippingMethodRequest $request, $id): JsonResponse
    {
        //
        //
        $shippingMethod=ShippingMethod::find($id);

        // این کد متد 'update' را در UserProduct فراخوانی می‌کند
        //$this->authorize('update', $category);

        // دریافت داده‌های معتبر
        $validatedData = $request->all();

        $shippingMethod->update($validatedData);

        $shippingMethod->updatedImageIfExist($request,$shippingMethod);

        // دوباره بارگیری کردن مدل از دیتابیس برای به‌روزرسانی اطلاعات
        $shippingMethod->refresh();


        return response()->json([
            'shippingMethod'=>new ShippingMethodResource($shippingMethod),
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
        $shippingMethod=ShippingMethod::find($id);
        //$this->authorize('delete', $category);

        $shippingMethod->deletedImageIfExist($request,$shippingMethod);

        $shippingMethod->delete();

        return response()->json([
            'message'=>$shippingMethod->name.'deleted',
        ]);
    }
}
