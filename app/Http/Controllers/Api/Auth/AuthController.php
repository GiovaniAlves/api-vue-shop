<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthFormRequest;
use App\Http\Requests\StoreUserFormRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private $repository;

    public function __construct(User $user)
    {
        $this->repository = $user;
    }

    /**
     * @param StoreUserFormRequest $request
     * @return \Illuminate\Http\Response
     */
    public function register(StoreUserFormRequest $request)
    {
        $request['password'] = bcrypt($request['password']);
        $user = $this->repository->create($request->all());

        $token = $user->createToken('vue-shop')->plainTextToken;

        return response([
            'user' => new UserResource($user),
            'token' => $token
        ]);
    }

    /**
     * @param AuthFormRequest $request
     * @return \Illuminate\Http\Response
     */
    public function login(AuthFormRequest $request)
    {
        $user = $this->repository->where('email', $request->email)->firstorFail();

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
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        // Revoke all tokens client.
        $user->tokens()->delete();

        return response([], 204);
    }
}
