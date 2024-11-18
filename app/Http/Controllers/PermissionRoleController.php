<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRoleRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PermissionRoleController extends Controller
{
    /**
     * @param PermissionRoleRequest $request
     * @return JsonResponse
     */
    public function revokePermissions(PermissionRoleRequest $request,$user_id): JsonResponse
    {
        //
        //$user=$request->user();
        $user=User::find($user_id);
        $user->revokePermissionTo($request->permission);

        return response()->json([
            'message'=>$user->name.'  revoke permission to  '.$request->permission,
        ]);

    }
    /**
     * @param PermissionRoleRequest $request
     * @return JsonResponse
     */
    public function givePermissions(PermissionRoleRequest $request,$user_id): JsonResponse
    {
        //
        //$user=$request->user();
        $user=User::find($user_id);
        $user->givePermissionTo($request->permission);

        return response()->json([
            'message'=>$user->name.'  give permission to  '.$request->permission,
        ]);
    }

    /**
     * @param PermissionRoleRequest $request
     * @return JsonResponse
     */
    public function removeRoles(PermissionRoleRequest $request,$user_id): JsonResponse
    {
        //
        //$user=$request->user();
        $user=User::find($user_id);
        $user->removeRole($request->role);

        return response()->json([
            'message'=>$user->name.'  remove role  '.$request->role,
        ]);
    }
    /**
     * @param PermissionRoleRequest $request
     * @return JsonResponse
     */
    public function assignRoles(PermissionRoleRequest $request,$user_id): JsonResponse
    {
        //
        //$user=$request->user();
        $user=User::find($user_id);
        $user->assignRole($request->role);

        return response()->json([
            'message'=>$user->name.'  assign role  '.$request->role,
        ]);
    }

}
