<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserFormRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;

class AuthController extends Controller
{
    /*** @var UserService */
    protected $userService;

    /*** @param UserService $userService */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param StoreUserFormRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function register(StoreUserFormRequest $request)
    {
        $user = $this->userService->createNewUser($request->all());

        $token = $user->createToken('vue-shop')->plainTextToken;

        return response([
            'user' => new UserResource($user),
            'token' => $token
        ]);
    }
}
