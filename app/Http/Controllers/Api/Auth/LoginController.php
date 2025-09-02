<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        return response()->json([
            'user' => new UserResource($request->user()),
            'token' => $request->user()->createToken($request->user()->name.' Token')->plainTextToken,
        ]);
    }
}
