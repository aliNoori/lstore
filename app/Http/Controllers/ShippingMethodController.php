<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShippingMethodRequest;
use App\Http\Resources\ShippingMethodResource;
use App\Models\ShippingMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ShippingMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $shippingMethods = ShippingMethod::all();
        return response()->json([
            'shippingMethods' => ShippingMethodResource::collection($shippingMethods),
        ]);
    }

    /**
     * Create a new shipping method.
     *
     * @param ShippingMethodRequest $request
     * @return ShippingMethodResource
     */
    public function create(ShippingMethodRequest $request): ShippingMethodResource
    {
        $validatedData = $request->validated();
        $shippingMethod = ShippingMethod::create($validatedData);
        $shippingMethod->addImage($request, $shippingMethod);

        return new ShippingMethodResource($shippingMethod);
    }

    /**
     * Display the specified shipping method.
     *
     * @param int $id
     * @return JsonResponse|ShippingMethodResource
     */
    public function show(int $id): JsonResponse|ShippingMethodResource
    {
        try {
            $shippingMethod = ShippingMethod::findOrFail($id);
            return new ShippingMethodResource($shippingMethod);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Shipping method not found'], 404);
        }
    }

    /**
     * Update the specified shipping method.
     *
     * @param ShippingMethodRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(ShippingMethodRequest $request, $id): JsonResponse
    {
        try {
            $shippingMethod = ShippingMethod::findOrFail($id);

            // Use the authorize method for authorization checks
            //$this->authorize('update', $shippingMethod);

            $validatedData = $request->validated();
            $shippingMethod->update($validatedData);
            $shippingMethod->updatedImageIfExist($request, $shippingMethod);

            return response()->json([
                'shippingMethod' => new ShippingMethodResource($shippingMethod),
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Shipping method not found'], 404);
        }
    }

    /**
     * Remove the specified shipping method.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function delete(Request $request, int $id): JsonResponse
    {
        try {
            $shippingMethod = ShippingMethod::findOrFail($id);

            // Use the authorize method for authorization checks
            //$this->authorize('delete', $shippingMethod);

            $shippingMethod->deletedImageIfExist($request, $shippingMethod);
            $shippingMethod->delete();

            return response()->json([
                'message' => $shippingMethod->name . ' deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Shipping method not found'], 404);
        }
    }
}
