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
    public function togglePermissions(PermissionRoleRequest $request,$email): JsonResponse
    {
        //
        //$user=$request->user();
        $user=User::where('email',$email)->first();

        if(!$user->hasDirectPermission($request->permission)){

            $user->givePermissionTo($request->permission);

            return response()->json([
                'success'=>true,
                'message'=>$user->name.'  give permission to  '.$request->permission,
            ]);
        }else{

            $user->revokePermissionTo($request->permission);
            return response()->json([
                'success'=>true,
                'message'=>$user->name.'  revoke permission to  '.$request->permission,
            ]);
        }




    }
    /**
     * @param PermissionRoleRequest $request
     * @return JsonResponse
     */
    public function toggleRoles(PermissionRoleRequest $request,$email): JsonResponse
    {
        //
        //$user=$request->user();
        $user=User::where('email',$email)->first();
        if(!$user->hasRole($request->role))
        {
            $user->assignRole($request->role);
            return response()->json([
                'success'=>true,
                'message'=>$user->name.'  assign role  '.$request->role,
            ]);
        }else{
            $user->removeRole($request->role);
            return response()->json([
                'success'=>true,
                'message'=>$user->name.'  remove role  '.$request->role,
            ]);
        }

    }

}
