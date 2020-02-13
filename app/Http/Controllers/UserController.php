<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;
use Sametsahindogan\ResponseObjectCreator\ErrorResult;
use Sametsahindogan\ResponseObjectCreator\ErrorService\ErrorBuilder;
use Sametsahindogan\ResponseObjectCreator\SuccessResult;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        return response()->json(new SuccessResult(['authed_user' => Auth::user()->toArray(),]));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignRole(Request $request, $id)
    {
        /** @var Validator $validator */
        $validator = validator($request->all(), [
            'role_id' => 'required',
        ])->setAttributeNames([
            'role_id' => 'Role',
        ]);

        if ($validator->fails()) {
            return response()->json(
                new ErrorResult(
                    (new ErrorBuilder())
                        ->title('Operation Failed')
                        ->message($validator->errors()->first())
                        ->extra($validator->errors()->all())
                )
            );
        }

        /** @var User $user */
        $user = User::find($id);

        if (!($user instanceof User)) {
            return response()->json(
                new ErrorResult(
                    (new ErrorBuilder())
                        ->title('Operation Failed')
                        ->message('User not found.')
                )
            );
        }

        $role[] = $request->get('role_id');

        $user->syncRoles($role);

        return response()->json(new SuccessResult());
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function givePermission(Request $request, $id)
    {
        /** @var Validator $validator */
        $validator = validator($request->all(), [
            'permissions' => 'required',
        ])->setAttributeNames([
            'permissions' => 'Permissions',
        ]);

        if ($validator->fails()) {
            return response()->json(
                new ErrorResult(
                    (new ErrorBuilder())
                        ->title('Operation Failed')
                        ->message($validator->errors()->first())
                        ->extra($validator->errors()->all())
                )
            );
        }

        /** @var User $user */
        $user = User::find($id);

        if (!($user instanceof User)) {
            return response()->json(
                new ErrorResult(
                    (new ErrorBuilder())
                        ->title('Operation Failed')
                        ->message('User not found.')
                )
            );
        }

        $permissions = $request->get('permissions');

        $user->syncPermissions($permissions);

        return response()->json(new SuccessResult());
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function banned(Request $request, $id)
    {
        /** @var User $user */
        $user = User::with('roles.permissions')->find($id);

        if (!($user instanceof User)) {
            return response()->json(
                new ErrorResult(
                    (new ErrorBuilder())
                        ->title('Operation Failed')
                        ->message('User not found.')
                )
            );
        }

        $user->status = $user->status === User::STATUS_BANNED ? User::STATUS_ACTIVE : User::STATUS_BANNED;
        $user->save();

        return response()->json(new SuccessResult());
    }
}
