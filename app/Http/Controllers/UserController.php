<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Events\OrderEvent;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\AddressResource;
use App\Http\Resources\CouponResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ScoreResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\WalletResource;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function testSocket(Request $request): void
    {
        broadcast(new MessageSent($request->message_public));
        broadcast(new OrderEvent($request->order_id));
    }

    public function profile(Request $request): UserResource
    {
        $user = $request->user();
        $this->authorize('view', $user);
        return new UserResource($user);
    }

    public function usersList(): JsonResponse
    {
        $users = User::all();
        return response()->json([
            'users_list' => UserResource::collection($users),
        ]);
    }

    public function create(UserRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        // Hash the password if provided
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user = User::create($validatedData);
        $user->addImage($request, $user);
        $user->assignRole('admin');  // Assign default role to admin

        // Token created for user
        $token = $user->createToken('authToken', ['read', 'write'])->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function show($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $this->authorize('view', $user);
            return response()->json([
                'message' => new UserResource($user),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        }
    }

    public function update(UserRequest $request, $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $this->authorize('update', $user);

            $validatedData = $request->validated();

            // Hash the password if provided
            if (!empty($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            }

            $user->update($validatedData);
            $user->updatedImageIfExist($request, $user);
            $user->refresh();

            return response()->json([
                'user' => new UserResource($user),
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        }
    }

    public function delete(Request $request, $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $this->authorize('delete', $user);

            $user->deletedImageIfExist($request, $user);
            $user->delete();

            return response()->json([
                'message' => $user->name . ' deleted successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        }
    }

    public function addAddress(AddressRequest $request): JsonResponse
    {
        $user = $request->user();
        $validatedData = $request->validated();
        $address = $user->addresses()->create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully',
            'address' => new AddressResource($address),
        ], 201);
    }

    public function editAddress(AddressRequest $request, $addressId): JsonResponse
    {
        $user = $request->user();
        $address = $user->addresses()->find($addressId);

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found',
            ], 404);
        }

        $validatedData = $request->validated();
        $address->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'address' => new AddressResource($address),
        ], 200);
    }

    public function addresses(Request $request): JsonResponse
    {
        $user = $request->user();
        $addresses = $user->addresses()->get();

        return response()->json([
            'success' => true,
            'addresses' => AddressResource::collection($addresses),
        ], 200);
    }
    public function addressShow(Request $request, $address_id): JsonResponse
    {
        $user = $request->user();

        // دریافت آدرس خاص بر اساس آدرس آیدی
        $address = $user->addresses()->where('id', $address_id)->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'address' => new AddressResource($address),
        ], 200);
    }


    public function myWallet(Request $request): JsonResponse
    {
        $user = $request->user();
        $wallet = $user->wallet()->first();

        return response()->json([
            'success' => true,
            'wallet' => new WalletResource($wallet),
        ], 200);
    }

    public function myScores(Request $request): JsonResponse
    {
        return $this->getUserResource($request, 'scores', ScoreResource::class);
    }

    public function myCoupons(Request $request): JsonResponse
    {
        return $this->getUserResource($request, 'coupons', CouponResource::class);
    }

    public function myOrders(Request $request): JsonResponse
    {
        return $this->getUserResource($request, 'orders', OrderResource::class);
    }

    private function getUserResource(Request $request, $relation, $resourceClass): JsonResponse
    {
        $user = $request->user();
        $data = $user->$relation()->get();

        return response()->json([
            'success' => true,
            $relation => $resourceClass::collection($data),
        ], 200);
    }
}
