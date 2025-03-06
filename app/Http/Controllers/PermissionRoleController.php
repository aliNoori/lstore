<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRoleRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class PermissionRoleController extends Controller
{
    /**
     * تغییر وضعیت مجوز کاربر (اضافه یا حذف).
     *
     * @param PermissionRoleRequest $request
     * @param string $email
     * @return JsonResponse
     */
    public function togglePermissions(PermissionRoleRequest $request, string $email): JsonResponse
    {
        // یافتن کاربر بر اساس ایمیل
        $user = User::where('email', $email)->firstOrFail();

        // بررسی اینکه آیا کاربر مجوز را دارد یا خیر
        if (!$user->hasDirectPermission($request->permission)) {
            $user->givePermissionTo($request->permission);

            return response()->json([
                'success' => true,
                'message' => "{$user->name} has been granted the '{$request->permission}' permission.",
            ]);
        }

        // در غیر این صورت مجوز حذف می‌شود
        $user->revokePermissionTo($request->permission);

        return response()->json([
            'success' => true,
            'message' => "{$user->name} has had the '{$request->permission}' permission revoked.",
        ]);
    }

    /**
     * تغییر وضعیت نقش کاربر (اضافه یا حذف).
     *
     * @param PermissionRoleRequest $request
     * @param string $email
     * @return JsonResponse
     */
    public function toggleRoles(PermissionRoleRequest $request, string $email): JsonResponse
    {
        // یافتن کاربر بر اساس ایمیل
        $user = User::where('email', $email)->firstOrFail();

        // بررسی اینکه آیا کاربر نقش را دارد یا خیر
        if (!$user->hasRole($request->role)) {
            $user->assignRole($request->role);

            return response()->json([
                'success' => true,
                'message' => "{$user->name} has been assigned the '{$request->role}' role.",
            ]);
        }

        // در غیر این صورت نقش حذف می‌شود
        $user->removeRole($request->role);

        return response()->json([
            'success' => true,
            'message' => "{$user->name} has had the '{$request->role}' role removed.",
        ]);
    }
}
