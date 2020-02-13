<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Sametsahindogan\ResponseObjectCreator\ErrorResult;
use Sametsahindogan\ResponseObjectCreator\ErrorService\ErrorBuilder;
use Sametsahindogan\ResponseObjectCreator\SuccessResult;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = validator($request->all(),
            [
                'email' => [
                    'required',
                    'string',
                    'max:255',
                    'min:6'
                ],
                'password' => [
                    'required',
                    'string',
                    'max:64',
                    'min:6'
                ],
            ]
        );

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
        $user = User::create([
            'status' => User::STATUS_ACTIVE,
            'name' => $request->get('email'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password'))
        ]);


        return response()->json(new SuccessResult($user->toArray()));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {

            $token = JWTAuth::attempt($credentials);

            if (!$token) {

                return response()->json(
                    new ErrorResult(
                        (new ErrorBuilder())
                            ->title('Operation Failed')
                            ->message('Wrong information.')
                            ->extra([])
                    )
                );
            }

        } catch (AuthorizationException|JWTException $e) {

            return response()->json(
                new ErrorResult(
                    (new ErrorBuilder())
                        ->title('Operation Failed')
                        ->message($e->getMessage())
                        ->extra([])
                )
            );
        }

        return response()->json(new SuccessResult(['token' => $token]));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {

            JWTAuth::invalidate($request->bearerToken());

        } catch (JWTException $e) {

            JWTAuth::unsetToken();

            return response()->json(
                new ErrorResult(
                    (new ErrorBuilder())
                        ->title('Operation Failed')
                        ->message($e->getMessage())
                        ->extra([])
                )
            );
        }

        return response()->json(new SuccessResult());

    }
}
