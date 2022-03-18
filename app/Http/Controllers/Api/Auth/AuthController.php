<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthFormRequest;
use App\Http\Requests\StoreUserFormRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    /**
     * @param AuthFormRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function login(AuthFormRequest $request)
    {
        $user = $this->userService->getUser($request->email);

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response(['message' => 'Credenciais Inválidas!'], 404);
        }

        $token = $user->createToken('vue-shop')->plainTextToken;

        return response([
            'user' => new UserResource($user),
            'token' => $token
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function user(Request $request)
    {
        $user = $request->user();

        return response([
            'user' => new UserResource($user)
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        // Revoke all tokens client...
        $user->tokens()->delete();

        return response([], 204);
    }
}
